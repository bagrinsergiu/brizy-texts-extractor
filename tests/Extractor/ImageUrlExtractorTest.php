<?php

namespace BrizyTextsExtractorTests\Extractor;

use BrizyTextsExtractor\Extractor\BrzTranslatableAttrExtractor;
use BrizyTextsExtractor\Extractor\CssImageUrlExtractor;
use BrizyTextsExtractor\Extractor\ImageUrlExtractor;
use BrizyTextsExtractor\Extractor\OldTextExtractor;
use BrizyTextsExtractor\Extractor\PlaceholderAttrExtractor;
use BrizyTextsExtractor\TextExtractor;
use PHPUnit\Framework\TestCase;
use function Sabre\Uri\parse;

class ImageUrlExtractorTest extends TestCase
{
    public function testExtract()
    {
        $content = file_get_contents('./tests/data/pages/case1.html.txt');

        $dom = new \DOMDocument();
        $dom->loadHTML($content, LIBXML_BIGLINES | LIBXML_NOBLANKS | LIBXML_NONET | LIBXML_NOERROR | LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD | LIBXML_PARSEHUGE);

        $extractor = new ImageUrlExtractor();

        $result = $extractor->extract($dom);

        $this->assertCount(25, $result, 'It should extract the corect amount of texts from attribute: data-brz-translateble-');

        $this->assertTrue(in_array('image1.jpg', $result), 'It should contain "image1.jpg"');
        $this->assertTrue(in_array('image2.jpg', $result), 'It should contain "image2.jpg"');
        $this->assertTrue(in_array('image3.jpg', $result), 'It should contain "image3.jpg"');
        $this->assertTrue(in_array('alt_for_image.jpg', $result), 'It should contain "alt_for_image.jpg"');
        $this->assertTrue(in_array('brizy-wp-pricing-1.jpg', $result), 'It should contain "brizy-wp-pricing-1.jpg"');
        $this->assertTrue(in_array('brizy-wp-pricing-2.jpg', $result), 'It should contain "brizy-wp-pricing-2.jpg"');
        $this->assertTrue(in_array('brizy-wp-pricing-11.jpg', $result), 'It should contain "brizy-wp-pricing-11.jpg"');
        $this->assertTrue(in_array('brizy-wp-pricing-22.jpg', $result), 'It should contain "brizy-wp-pricing-22.jpg"');
        $this->assertTrue(in_array('brizy-wp-pricing-13.jpg', $result), 'It should contain "brizy-wp-pricing-13.jpg"');
        $this->assertTrue(in_array('brizy.jpg', $result), 'It should contain "brizy.jpg"');
        $this->assertTrue(in_array('alt_for_image.jpg', $result), 'It should contain "alt_for_image.jpg"');
        $this->assertTrue(in_array('next-alt', $result), 'It should contain "next-alt"');

    }

}
