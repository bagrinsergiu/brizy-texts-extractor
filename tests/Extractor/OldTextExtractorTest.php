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

class OldTextExtractorTest extends TestCase
{
    public function testExtract()
    {
        $content = file_get_contents('./tests/data/pages/case1.html.txt');

        $dom = new \DOMDocument();
        $dom->loadHTML($content, LIBXML_BIGLINES | LIBXML_NOBLANKS | LIBXML_NONET | LIBXML_NOERROR | LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD | LIBXML_PARSEHUGE);

        $extractor = new OldTextExtractor();

        $result = $extractor->extract($dom);

        $this->assertCount(15, $result, 'It should extract the corect amount of texts from attribute: data-brz-translatable-');

        $this->assertTrue(in_array('title', $result), 'It should contain "title"');
        $this->assertTrue(in_array("{{placeholder}}placeholder{{unknown_placeholder}} text{{end_placeholder}}
text {{placeholder}}placeholder text1", $result), 'It should contain "text"');
        $this->assertTrue(in_array("{{end_placeholder}} text2", $result), 'It should contain "text2"');
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


        $this->assertFalse(in_array('NOT INCLUDE STYLE FROM HEAD', $result), 'It should contain "NOT INCLUDE STYLE FROM HEAD"');
        $this->assertFalse(in_array('NOT INCLUDE SCRIPT FROM HEAD', $result), 'It should contain "NOT INCLUDE SCRIPT FROM HEAD"');
        $this->assertFalse(in_array('NOT INCLUDE STYLE FROM HEAD', $result), 'It should contain "NOT INCLUDE STYLE FROM BODY"');
        $this->assertFalse(in_array('NOT INCLUDE SCRIPT FROM HEAD', $result), 'It should contain "NOT INCLUDE SCRIPT FROM BODY"');
        $this->assertFalse(in_array('IGNORED TEXT', $result), 'It should contain "IGNORED TEXT"');

    }

}
