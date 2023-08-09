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

class CssImageUrlExtractorTest extends TestCase
{
    public function testExtract()
    {
        $content = file_get_contents('./tests/data/pages/case1.html.txt');

        $dom = new \DOMDocument();
        $dom->loadHTML($content, LIBXML_BIGLINES | LIBXML_NOBLANKS | LIBXML_NONET | LIBXML_NOERROR | LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD | LIBXML_PARSEHUGE);

        $extractor = new CssImageUrlExtractor();

        $result = $extractor->extract($dom);

        $this->assertCount(10, $result,'It should extract the corect amount of texts from attribute: data-brz-translateble-');

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
