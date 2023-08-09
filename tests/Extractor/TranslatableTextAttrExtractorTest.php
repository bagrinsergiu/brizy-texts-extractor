<?php

namespace BrizyTextsExtractorTests\Extractor;

use BrizyTextsExtractor\Extractor\BrzTranslatableAttrExtractor;
use BrizyTextsExtractor\Extractor\CssImageUrlExtractor;
use BrizyTextsExtractor\Extractor\ImageUrlExtractor;
use BrizyTextsExtractor\Extractor\OldTextExtractor;
use BrizyTextsExtractor\Extractor\PlaceholderAttrExtractor;
use BrizyTextsExtractor\Extractor\TranslatableTextAttrExtractor;
use BrizyTextsExtractor\TextExtractor;
use PHPUnit\Framework\TestCase;
use function Sabre\Uri\parse;

class TranslatableTextAttrExtractorTest extends TestCase
{
    public function testExtract()
    {
        $content = file_get_contents('./tests/data/pages/case1.html.txt');

        $dom = new \DOMDocument();
        $dom->loadHTML($content, LIBXML_BIGLINES | LIBXML_NOBLANKS | LIBXML_NONET | LIBXML_NOERROR | LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD | LIBXML_PARSEHUGE);

        $extractor = new TranslatableTextAttrExtractor();

        $result = $extractor->extract($dom);

        $this->assertCount(1, $result, 'It should extract the correct amount of texts');

        $this->assertTrue(in_array("Type the entire URL of your website in the text box on the left. ... The website translations you get with Google Translate aren't accurate and the service", $result), 'It should contain the correct text');
    }
}
