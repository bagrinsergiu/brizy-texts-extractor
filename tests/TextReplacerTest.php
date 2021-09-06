<?php

namespace BrizyTextsExtractorTests;

use BrizyTextsExtractor\TextExtractor;
use BrizyTextsExtractor\TextReplacer;
use PHPUnit\Framework\TestCase;

class TextReplacerTest extends TestCase
{

    public function testExtractFromContentCase1()
    {
        $extractor = new TextExtractor();
        $result = $extractor->extractFromContent(file_get_contents('./tests/data/pages/case1.html'));

        // add fake translated content
        foreach($result as $i=>$extractedContent) {
            $extractedContent->setTranslatedContent('text'.$i);
        }

        $replacer = new TextReplacer();
        $content =$replacer->replace(file_get_contents('./tests/data/pages/case1.html'),$result);

        foreach($result as $i=>$extractedContent) {
            $this->assertStringContainsString('text'.$i,$content,'It should contain "text"'.$i.'"');
        }
    }
}
