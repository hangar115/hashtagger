# Auto-Hashtagger for article titles
Auto-hashtag an article title based on content

Inspired by https://gist.github.com/shlomibabluki/6612871 

## Installation
Installation is via composer:
```
composer require ampersa/hashtagger
```

## Usage
### Basic usage
```php
$title = 'An article about PHP and hashtags';
$content = 'PHP is a server-side scripting language designed primarily for web development but also used as a general-purpose programming language. Originally created by Rasmus Lerdorf in 1994,[4] the PHP reference implementation is now produced by The PHP Development Team.[5] PHP originally stood for Personal Home Page,[4] but it now stands for the recursive acronym PHP: Hypertext Preprocessor.[6]

A hashtag is a type of label or metadata tag used on social network and microblogging services which makes it easier for users to find messages with a specific theme or content. Users create and use hashtags by placing the hash character # (also known as the number sign or pound sign) in front of a word or unspaced phrase, either in the main text of a message or at the end. Searching for that hashtag will yield each message that has been tagged with it. A hashtag archive is consequently collected into a single stream under the same hashtag.[1] For example, on the photo-sharing service Instagram, the hashtag #bluesky allows users to find all the posts that have been tagged using that hashtag.';

$tagger = new Hashtagger($title, $content);
$tagged = $tagger->tag();

// Result: An article about #PHP and #hashtags
```

### Options
Tweak the ratio of hashtags for the title using the sole argument to tag():
```php
$tagger = new Hashtagger($title, $content);
$tagged = $tagger->tag(0.7);

// Result: An #article about #PHP and #hashtags
```