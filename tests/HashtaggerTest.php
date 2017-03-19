<?php

use PHPUnit\Framework\TestCase;
use Ampersa\Hashtagger\Hashtagger;

class HashtaggerTest extends TestCase
{
    /**
     *
     * @return void
     */
    public function testHashtagger()
    {
        $title = 'Leeds-based businesses support local homeless charity';
        $text = 'A West Yorkshire-based homeless charity, Simon on the Streets, has launched a corporate partnerships scheme named ‘5 for 5’. 

A launch event for the initiative took place on the 9th March 2017, where Simon on the Streets’ staff, trustees and thirty local businesses joined together for an evening of networking at The Double Tree Hilton in Leeds.

Founded in 1999, Simon on the Streets is an independent charity, working with local people who are affected by homelessness and rough sleeping. The organisation offers street-support to individuals who have complex needs and cannot access mainstream services, due to behavioural issues or mental illness. 

The charity works with local businesses to fulfil corporate social responsibility objectives and give back to the community. Businesses are encouraged to join the initiative by donating £500 per year for the next five years. All contributions go towards supporting those affected by homelessness in the Leeds, Bradford and Huddersfield areas. 

All donating businesses receive a certificate of partnership, the opportunity to attend valuable networking events and various other promotional opportunities. 
One of the first businesses to enter the scheme is Leeds First Direct Arena, an entertainment venue based in the city centre. 

Director of sales, marketing & PR at the venue, Tony Watson commented:

“First Direct Arena are delighted to be able to offer our continued support to Simon on the Streets. They\re an incredible charity who are there for people when no one else is - saving lives and improving the outlook for rough sleepers. We sincerely hope that the sponsorship will assist them in continuing this great work.” 

Simon on the Streets development manager commented:

‘We’re delighted to launch the new ‘5 for 5’ partnership scheme which will allow us to continue delivering the necessary practical and emotional support and guidance that Simon on the Streets’ service users require. The rate of homelessness in city centres is still disturbingly high and as such, the services we provide are key in tackling the problem. Each homeless individual has a unique story and as a result requires tailor-made and intensive support that mainstream services cannot provide. We’re keen to help every homeless person, no matter how long it takes to resolve their unique issues. Unfortunately, public funding does not allow us to work in this way and so we rely on the kind support of businesses and other fundraising initiatives in order to operate.”

To find out more about the ‘5 for 5’ scheme visit www.simononthestreets.co.uk or contact Simon the Streets development manager, Sue Oliver at sue@simononthestreets.co.uk.';

        $tagger = new Hashtagger($title, $text);
        $result = $tagger->tag();

        $this->assertEquals('Leeds-based #businesses support local #homeless charity', $result);
    }

    /**
     *
     * @return void
     */
    public function testHashtaggerRatio()
    {
        $title = 'Leeds-based businesses support local homeless charity';
        $text = 'A West Yorkshire-based homeless charity, Simon on the Streets, has launched a corporate partnerships scheme named ‘5 for 5’. 

A launch event for the initiative took place on the 9th March 2017, where Simon on the Streets’ staff, trustees and thirty local businesses joined together for an evening of networking at The Double Tree Hilton in Leeds.

Founded in 1999, Simon on the Streets is an independent charity, working with local people who are affected by homelessness and rough sleeping. The organisation offers street-support to individuals who have complex needs and cannot access mainstream services, due to behavioural issues or mental illness. 

The charity works with local businesses to fulfil corporate social responsibility objectives and give back to the community. Businesses are encouraged to join the initiative by donating £500 per year for the next five years. All contributions go towards supporting those affected by homelessness in the Leeds, Bradford and Huddersfield areas. 

All donating businesses receive a certificate of partnership, the opportunity to attend valuable networking events and various other promotional opportunities. 
One of the first businesses to enter the scheme is Leeds First Direct Arena, an entertainment venue based in the city centre. 

Director of sales, marketing & PR at the venue, Tony Watson commented:

“First Direct Arena are delighted to be able to offer our continued support to Simon on the Streets. They\re an incredible charity who are there for people when no one else is - saving lives and improving the outlook for rough sleepers. We sincerely hope that the sponsorship will assist them in continuing this great work.” 

Simon on the Streets development manager commented:

‘We’re delighted to launch the new ‘5 for 5’ partnership scheme which will allow us to continue delivering the necessary practical and emotional support and guidance that Simon on the Streets’ service users require. The rate of homelessness in city centres is still disturbingly high and as such, the services we provide are key in tackling the problem. Each homeless individual has a unique story and as a result requires tailor-made and intensive support that mainstream services cannot provide. We’re keen to help every homeless person, no matter how long it takes to resolve their unique issues. Unfortunately, public funding does not allow us to work in this way and so we rely on the kind support of businesses and other fundraising initiatives in order to operate.”

To find out more about the ‘5 for 5’ scheme visit www.simononthestreets.co.uk or contact Simon the Streets development manager, Sue Oliver at sue@simononthestreets.co.uk.';

        $tagger = new Hashtagger($title, $text);
        $result = $tagger->tag(0.6);

        $this->assertEquals('Leeds-based #businesses #support local #homeless charity', $result);
    }
}
