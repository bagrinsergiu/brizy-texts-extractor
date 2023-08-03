<?php

namespace BrizyTextsExtractorTests;

use BrizyTextsExtractor\Extractor\BrzTranslatableAttrExtractor;
use BrizyTextsExtractor\Extractor\CssImageUrlExtractor;
use BrizyTextsExtractor\Extractor\ImageUrlExtractor;
use BrizyTextsExtractor\Extractor\OldTextExtractor;
use BrizyTextsExtractor\Extractor\PlaceholderAttrExtractor;
use BrizyTextsExtractor\TextExtractor;
use PHPUnit\Framework\TestCase;
use function Sabre\Uri\parse;

class TextExtractorTest extends TestCase
{

    public function testExtractFromContentCase1()
    {
        $domExtractors = [
            new PlaceholderAttrExtractor(),
            new BrzTranslatableAttrExtractor(),
            new CssImageUrlExtractor(),
            new ImageUrlExtractor(),
            new OldTextExtractor(),
        ];
        $extractor = new TextExtractor($domExtractors);
        $result = $extractor->extractFromContent(file_get_contents('./tests/data/pages/case1.html'));

        $this->assertCount(43, $result, 'It should return the correct count of texts');

        $this->assertTrue(in_array('The website translations1', $result), 'It should contain "The website translations1"');
        $this->assertTrue(in_array('The website translations2', $result), 'It should contain "The website translations2"');
        $this->assertTrue(in_array('The website translations3', $result), 'It should contain "The website translations3"');
        $this->assertTrue(in_array('title', $result), 'It should contain "title"');
        $this->assertTrue(in_array('text', $result), 'It should contain "text"');
        $this->assertTrue(in_array('text2', $result), 'It should contain "text2"');
        $this->assertTrue(in_array('placeholder-in-placeholder', $result), 'It should contain "placeholder-in-placeholder"');
        $this->assertTrue(in_array('placeholder text1', $result), 'It should contain "placeholder text1"');
        $this->assertTrue(in_array('placeholder', $result), 'It should contain "placeholder text"');
        $this->assertTrue(
            in_array(
                "Type the entire URL of your website in the text box on the left. ... The website translations you get with Google Translate aren't accurate and the service",
                $result
            ),
            'It should contain "the long text"'
        );
        $this->assertTrue(in_array('src', $result), 'It should contain "src"');
        $this->assertTrue(in_array('src="image1.jpg"', $result), 'It should contain src="image1.jpg"');
        $this->assertTrue(in_array('paragraph1', $result), 'It should contain "paragraph1"');
        $this->assertTrue(in_array('paragraph2', $result), 'It should contain "paragraph2"');
        $this->assertTrue(in_array('paragraph3', $result), 'It should contain "paragraph3"');
        $this->assertTrue(in_array('italic', $result), 'It should contain "italic"');
        $this->assertTrue(in_array('bold', $result), 'It should contain "bold"');
        $this->assertTrue(in_array('div1', $result), 'It should contain "div1"');
        $this->assertTrue(in_array('div3', $result), 'It should contain "div2"');
        $this->assertTrue(in_array('div2', $result), 'It should contain "div3"');
        $this->assertTrue(in_array('span', $result), 'It should contain "span"');
        $this->assertTrue(in_array('image1.jpg', $result), 'It should contain "image1.jpg"');
        $this->assertTrue(in_array('image2.jpg', $result), 'It should contain "image2.jpg"');
        $this->assertTrue(in_array('image3.jpg', $result), 'It should contain "image3.jpg"');
        $this->assertTrue(in_array('alt_for_image.jpg', $result), 'It should contain "alt_for_image.jpg"');
        $this->assertTrue(in_array('next-alt', $result), 'It should contain "next-alt"');
        $this->assertTrue(in_array('placeholder', $result), 'It should contain "placeholder"');

        $this->assertTrue(in_array('brizy-wp-pricing-1.jpg', $result), 'It should contain "brizy-wp-pricing-1.jpg"');
        $this->assertTrue(in_array('brizy-wp-pricing-2.jpg', $result), 'It should contain "brizy-wp-pricing-2.jpg"');
        $this->assertTrue(in_array('brizy-wp-pricing-11.jpg', $result), 'It should contain "brizy-wp-pricing-11.jpg"');
        $this->assertTrue(in_array('brizy-wp-pricing-22.jpg', $result), 'It should contain "brizy-wp-pricing-22.jpg"');
        $this->assertTrue(in_array('brizy-wp-pricing-13.jpg', $result), 'It should contain "brizy-wp-pricing-13.jpg"');
        $this->assertTrue(in_array('brizy.jpg', $result), 'It should contain "brizy.jpg"');

        $this->assertFalse(in_array('NOT INCLUDE STYLE FROM HEAD', $result), 'It should contain "NOT INCLUDE STYLE FROM HEAD"');
        $this->assertFalse(in_array('NOT INCLUDE SCRIPT FROM HEAD', $result), 'It should contain "NOT INCLUDE SCRIPT FROM HEAD"');
        $this->assertFalse(in_array('NOT INCLUDE STYLE FROM HEAD', $result), 'It should contain "NOT INCLUDE STYLE FROM BODY"');
        $this->assertFalse(in_array('NOT INCLUDE SCRIPT FROM HEAD', $result), 'It should contain "NOT INCLUDE SCRIPT FROM BODY"');

        $this->assertTrue(in_array("./logo-header1.svg", $result), 'It should contain "logo-header1.svg"');
        $this->assertTrue(in_array("./logo-header2.svg", $result), 'It should contain "./logo-header2.svg"');
        $this->assertTrue(in_array("./logo-header3.svg", $result), 'It should contain "./logo-header3.svg"');
        $this->assertTrue(in_array("./logo-header4.svg", $result), 'It should contain "./logo-header4.svg"');
        $this->assertTrue(in_array("./logo-header5.svg", $result), 'It should contain "./logo-header5.svg"');
        $this->assertTrue(in_array("./logo-header6.svg", $result), 'It should contain "./logo-header6.svg"');
        $this->assertTrue(in_array("http://domain.com/logo-header7.svg", $result), 'It should contain  "http://domain.com/logo-header7.svg"');
        $this->assertTrue(in_array("http://domain.com/logo-header8.svg", $result), 'It should contain  "http://domain.com/logo-header8.svg"');
        $this->assertTrue(in_array("http://domain.com/logo-header9.svg", $result), 'It should contain  "http://domain.com/logo-header9.svg"');
        $this->assertTrue(in_array("http://domain.com/logo-header10.svg", $result), 'It should contain "http://domain.com/logo-header10.svg"');

    }

}
