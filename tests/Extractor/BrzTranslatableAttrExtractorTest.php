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

class BrzTranslatableAttrExtractorTest extends TestCase
{
    public function testExtract()
    {
        $content = file_get_contents('./tests/data/pages/case1.html');

        $dom = new \DOMDocument();
        $dom->loadHTML($content, LIBXML_BIGLINES | LIBXML_NOBLANKS | LIBXML_NONET | LIBXML_NOERROR | LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD | LIBXML_PARSEHUGE);

        $extractor = new BrzTranslatableAttrExtractor();

        $result = $extractor->extract($dom);

        $this->assertCount(3, $result,'It should extract the corect amount of texts from attribute: data-brz-translateble-');
        $this->assertTrue(in_array('The website translations1', $result), 'It should contain "The website translations1"');
        $this->assertTrue(in_array('The website translations2', $result), 'It should contain "The website translations2"');
        $this->assertTrue(in_array('The website translations3', $result), 'It should contain "The website translations3"');
    }

}
