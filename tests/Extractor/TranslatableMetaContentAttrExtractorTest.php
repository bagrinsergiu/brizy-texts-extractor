<?php

namespace BrizyTextsExtractorTests\Extractor;

use BrizyTextsExtractor\Extractor\BrzTranslatableAttrExtractor;
use BrizyTextsExtractor\Extractor\CssImageUrlExtractor;
use BrizyTextsExtractor\Extractor\ImageUrlExtractor;
use BrizyTextsExtractor\Extractor\OldTextExtractor;
use BrizyTextsExtractor\Extractor\PlaceholderAttrExtractor;
use BrizyTextsExtractor\Extractor\TranslatableMetaContentAttrExtractor;
use BrizyTextsExtractor\Extractor\TranslatableTextAttrExtractor;
use BrizyTextsExtractor\TextExtractor;
use PHPUnit\Framework\TestCase;
use function Sabre\Uri\parse;

class TranslatableMetaContentAttrExtractorTest extends TestCase
{
    public function testExtract()
    {
        $content = file_get_contents('./tests/data/pages/case1.html.txt');

        $dom = new \DOMDocument();
        $dom->loadHTML($content, LIBXML_BIGLINES | LIBXML_NOBLANKS | LIBXML_NONET | LIBXML_NOERROR | LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD | LIBXML_PARSEHUGE);

        $extractor = new TranslatableMetaContentAttrExtractor();

        $result = $extractor->extract($dom);

        $this->assertCount(3, $result, 'It should extract the correct amount of texts');

        $this->assertTrue(in_array('og:title', $result), 'It should contain "og:title"');
        $this->assertTrue(in_array('twitter:title', $result), 'It should contain "twitter:title"');
        $this->assertTrue(in_array('og:description', $result), 'It should contain "og:description"');
    }
}
