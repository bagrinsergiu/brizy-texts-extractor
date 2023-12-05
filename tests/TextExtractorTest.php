<?php

namespace BrizyTextsExtractorTests;

use BrizyTextsExtractor\TextExtractor;
use BrizyTextsExtractor\TextReplacer;
use PHPUnit\Framework\TestCase;
use function Sabre\Uri\parse;

class TextExtractorTest extends TestCase
{

    public function testExtractFromContentCase1()
    {
        $extractor = new TextExtractor();
        $result = $extractor->extractFromContent(file_get_contents('/opt/project/tests/data/pages/case1.html'));

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

        $this->assertFalse(in_array('data:image/svg+xml;base64,PHN2ZyB2aWV3Qm9', $result), 'It should NOT contain "data:image/svg+xml;base64,PHN2ZyB2aWV3Qm9"');
        $this->assertFalse(in_array('NOT INCLUDE STYLE FROM HEAD', $result), 'It should NOT contain "NOT INCLUDE STYLE FROM HEAD"');
        $this->assertFalse(in_array('NOT INCLUDE SCRIPT FROM HEAD', $result), 'It should NOT contain "NOT INCLUDE SCRIPT FROM HEAD"');
        $this->assertFalse(in_array('NOT INCLUDE STYLE FROM HEAD', $result), 'It should NOT contain "NOT INCLUDE STYLE FROM BODY"');
        $this->assertFalse(in_array('NOT INCLUDE SCRIPT FROM HEAD', $result), 'It should NOT contain "NOT INCLUDE SCRIPT FROM BODY"');

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

    public function testExtractSimpleTextsFromContent()
    {
        $extractor = new TextExtractor();
        $result = $extractor->extractSimpleTextsFromContent(file_get_contents('/opt/project/tests/data/pages/case1.html'));

        $this->assertCount(28, $result, 'It should return the correct count of texts');

        $this->assertFalse(in_array('text', $result), 'It should contain "text"');
        $this->assertFalse(in_array('text2', $result), 'It should contain "text2"');
        $this->assertFalse(in_array('placeholder text1', $result), 'It should contain "placeholder text1"');
        $this->assertFalse(in_array(
            "Type the entire URL of your website in the text box on the left. ... The website translations you get with Google Translate aren't accurate and the service",
            $result
        ),
            'It should contain "the long text"'
        );
        $this->assertFalse(in_array('src', $result), 'It should contain "src"');
        $this->assertFalse(in_array('src="image1.jpg"', $result), 'It should contain src="image1.jpg"');
        $this->assertFalse(in_array('paragraph1', $result), 'It should contain "paragraph1"');
        $this->assertFalse(in_array('paragraph2', $result), 'It should contain "paragraph2"');
        $this->assertFalse(in_array('paragraph3', $result), 'It should contain "paragraph3"');
        $this->assertFalse(in_array('italic', $result), 'It should contain "italic"');
        $this->assertFalse(in_array('bold', $result), 'It should contain "bold"');
        $this->assertFalse(in_array('div1', $result), 'It should contain "div1"');
        $this->assertFalse(in_array('div3', $result), 'It should contain "div2"');
        $this->assertFalse(in_array('div2', $result), 'It should contain "div3"');
        $this->assertFalse(in_array('span', $result), 'It should contain "span"');


        $this->assertTrue(in_array('The website translations1', $result), 'It should contain "The website translations1"');
        $this->assertTrue(in_array('The website translations2', $result), 'It should contain "The website translations2"');
        $this->assertTrue(in_array('The website translations3', $result), 'It should contain "The website translations3"');
        $this->assertTrue(in_array('title', $result), 'It should contain "title"');
        $this->assertTrue(in_array('placeholder-in-placeholder', $result), 'It should contain "placeholder-in-placeholder"');
        $this->assertTrue(in_array('placeholder', $result), 'It should contain "placeholder text"');
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

        $this->assertFalse(in_array('NOT INCLUDE STYLE FROM HEAD', $result), 'It should contain "NOT INCLUDE STYLE FROM HEAD"');
        $this->assertFalse(in_array('NOT INCLUDE SCRIPT FROM HEAD', $result), 'It should contain "NOT INCLUDE SCRIPT FROM HEAD"');
        $this->assertFalse(in_array('NOT INCLUDE STYLE FROM HEAD', $result), 'It should contain "NOT INCLUDE STYLE FROM BODY"');
        $this->assertFalse(in_array('NOT INCLUDE SCRIPT FROM HEAD', $result), 'It should contain "NOT INCLUDE SCRIPT FROM BODY"');

    }

    public function testMetaFromContent()
    {
        $extractor = new TextExtractor();
        $result = $extractor->extractFromContent(file_get_contents('/opt/project/tests/data/pages/meta.html'));

        $this->assertCount(5, $result, 'It should return the correct count of texts');

        $this->assertFalse(in_array('IGNORE', $result), 'It should contain "IGNORE"');
        $this->assertFalse(in_array('twitter:card', $result), 'It should contain "twitter:card"');
        $this->assertFalse(in_array('og:site_name', $result), 'It should contain "og:site_name"');
        $this->assertFalse(in_array('og:type', $result), 'It should contain "og:type"');

        $this->assertTrue(in_array('og:title', $result), 'It should contain "og:title"');
        $this->assertTrue(in_array('og:description', $result), 'It should contain "og:description"');
        $this->assertTrue(in_array('og:url', $result), 'It should contain "og:url"');
        $this->assertTrue(in_array('og:image', $result), 'It should contain "og:image"');
    }

}
