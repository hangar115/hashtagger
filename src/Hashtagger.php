<?php
/**
 * Article Hashtagger
 *
 * Copyright (c) 2017 Ampersa Ltd.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a
 * copy of this software and associated documentation files (the "Software"),
 * to deal in the Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute, sublicense,
 * and/or sell copies of the Software, and to permit persons to whom the
 * Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER
 * DEALINGS IN THE SOFTWARE.
 *
 * @author Ampersa Ltd. <contact@ampersa.co.uk>
 * @license MIT
 */
namespace Ampersa\Hashtagger;

use InvalidArgumentException;
use NlpTools\Stemmers\PorterStemmer;
use NlpTools\Tokenizers\WhitespaceAndPunctuationTokenizer;
use NlpTools\Utils\Normalizers\English as EnglishNormalizer;

class Hashtagger
{
    /** @var string */
    protected $title;

    /** @var string */
    protected $content;

    /** @var float */
    protected $ratio = 0.3;

    /** @var array */
    protected $index = [];

    /** @var array */
    protected $stopwords = [];

    /** @var array */
    protected $originalTokens = [];

    /** @var Stemmer */
    protected $stemmer;

    /**
     * Construct the class and set the input
     * @param string $title
     * @param string $content
     * @param string $lang
     */
    public function __construct($title, $content, $lang = 'en')
    {
        $this->stopwords = $this->loadStopwords($lang);

        $this->stemmer = new PorterStemmer;

        $this->title = $this->addStringToIndex($title, true);
        $this->content = $this->addStringToIndex($content);
    }

    /**
     * Calculate and return the tagged title
     * @param float|null $ratio
     * @return string
     */
    public function tag(float $ratio = null) : string
    {
        if (!empty($ratio)) {
            $this->ratio = $ratio;
        }

        $total = floor(count($this->title[1])*$this->ratio);

        $ranks = [];
        foreach ($this->title[1] as $token) {
            $ranks[$token] = $this->index[$token];
        }

        arsort($ranks);

        foreach (array_keys(array_slice($ranks, 0, $total)) as $token) {
            $this->title[2][$token] = sprintf('#%s', $this->title[2][$token]);
        }

        $previousTag = null;
        $previousMerged = false;
        $taggedTitle = [];

        foreach ($this->title[1] as $token) {
            $word = $this->title[2][$token];
            if ($word{0} == '#') {
                if (!empty($previousTag)) {
                    $mergedTag = $this->mergeTags($previousTag, $word, ($previousMerged == true));
                    if ($mergedTag) {
                        array_pop($taggedTitle);
                        $taggedTitle[] = $mergedTag;
                        $previousTag = $mergedTag;
                        $previousMerged = true;
                        continue;
                    } else {
                        $taggedTitle[] = $word;
                    }
                } else {
                    $taggedTitle[] = $word;
                }

                $previousTag = $word;
            } else {
                $taggedTitle[] = $word;
                $previousTag = null;
            }

            $previousMerged = false;
        }

        return trim(str_replace(' , ', ', ', implode(' ', $taggedTitle)));
    }

    /**
     * Tokenizes and adds a string to the index
     * @param string  $string
     * @param boolean $title
     */
    protected function addStringToIndex($string, $title = false) : array
    {
        $previousToken = null;
        $stemmedTokens = [];
        $originalTokens = [];

        $string = mb_convert_encoding($string, 'utf-8');
        $string = str_replace(["\n", "\r"], '', $string);

        $tokens = (new WhitespaceAndPunctuationTokenizer)
                    ->tokenize($string);

        foreach ($tokens as $token) {
            $stemmedToken = $this->stemmer->stem($token);

            if (in_array($stemmedToken, $this->stopwords) and !$title) {
                $previousToken = null;
                continue;
            }

            if (strlen($stemmedToken) < 2) {
                $previousToken = null;
                continue;
            }

            // Unigram
            if (!isset($this->index[$stemmedToken])) {
                $this->index[$stemmedToken] = 0;
            }

            $this->index[$stemmedToken] += 1;

            // Bigram
            if (!empty($previousToken)) {
                if (!isset($this->index[sprintf('%s %s', $previousToken, $stemmedToken)])) {
                    $this->index[sprintf('%s %s', $previousToken, $stemmedToken)] = 0;
                }
                $this->index[sprintf('%s %s', $previousToken, $stemmedToken)] += 1.5;
            }

            $previousToken = $stemmedToken;

            $originalTokens[$stemmedToken] = $token;

            $stemmedTokens[] = $stemmedToken;
        }

        return [$string, $stemmedTokens, $originalTokens];
    }

    protected function mergeTags($tag1, $tag2, $multigram = false) : string
    {
        $tag1 = str_replace('#', '', $tag1);
        $tag2 = str_replace('#', '', $tag2);

        if (!$multigram) {
            $merged = sprintf('%s %s', $this->stemmer->stem($tag1), $this->stemmer->stem($tag2));

            if (!isset($this->index[$merged])) {
                return false;
            }

            if (($this->index[$merged] / $this->index[$this->stemmer->stem($tag1)]) < 0.5) {
                return false;
            }

            if (($this->index[$merged] / $this->index[$this->stemmer->stem($tag2)]) < 0.5) {
                return false;
            }
        }

        return sprintf('#%s%s', $tag1, $tag2);
    }

    /**
     * Return a list of stopwords from a file
     * @param  string $lang
     * @return array
     */
    protected function loadStopwords($lang) : array
    {
        $stopwordFile = sprintf('stopwords_%s.txt', $lang);

        if (file_exists('./data/'.$stopwordFile)) {
            return array_filter(
                array_map(
                    'trim',
                    file('./data/'.$stopwordFile)
                )
            );
        }

        throw new InvalidArgumentException(sprintf('A stopwords file for "%s" could not be located', $lang));
    }
}