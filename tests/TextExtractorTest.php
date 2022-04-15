<?php

namespace BrizyTextsExtractorTests;

use BrizyTextsExtractor\TextExtractor;
use PHPUnit\Framework\TestCase;

class TextExtractorTest extends TestCase
{

    public function testExtractFromContentCase1()
    {
        $extractor = new TextExtractor();
        $result    = $extractor->extractFromContent(file_get_contents('./tests/data/pages/case1.html'));

        $this->assertCount(26, $result, 'It should return the correct count of texts');

        $this->assertTrue(in_array('title', $result), 'It should container "title"');
        $this->assertTrue(
            in_array(
                "Type the entire URL of your website in the text box on the left. ... The website translations you get with Google Translate aren't accurate and the service",
                $result
            ),
            'It should container "the long text"'
        );
        $this->assertTrue(in_array('src', $result), 'It should container "src"');
        $this->assertTrue(in_array('src="image1.jpg"', $result), 'It should container src="image1.jpg"');
        $this->assertTrue(in_array('paragraph1', $result), 'It should container "paragraph1"');
        $this->assertTrue(in_array('paragraph2', $result), 'It should container "paragraph2"');
        $this->assertTrue(in_array('paragraph3', $result), 'It should container "paragraph3"');
        $this->assertTrue(in_array('italic', $result), 'It should container "italic"');
        $this->assertTrue(in_array('bold', $result), 'It should container "bold"');
        $this->assertTrue(in_array('div1', $result), 'It should container "div1"');
        $this->assertTrue(in_array('div3', $result), 'It should container "div2"');
        $this->assertTrue(in_array('div2', $result), 'It should container "div3"');
        $this->assertTrue(in_array('span', $result), 'It should container "span"');
        $this->assertTrue(in_array('image1.jpg', $result), 'It should container "image1.jpg"');
        $this->assertTrue(in_array('image2.jpg', $result), 'It should container "image2.jpg"');
        $this->assertTrue(in_array('image3.jpg', $result), 'It should container "image3.jpg"');
        $this->assertTrue(in_array('alt_for_image.jpg', $result), 'It should container "alt_for_image.jpg"');
        $this->assertTrue(in_array('next-alt', $result), 'It should container "next-alt"');
        $this->assertTrue(in_array('placeholder', $result), 'It should container "placeholder"');

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
    }

}
