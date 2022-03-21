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
        $result    = $extractor->extractFromContent(file_get_contents('./tests/data/pages/case1.html'));

        // add fake translated content
        foreach ($result as $i => $extractedContent) {
            $extractedContent->setTranslatedContent($extractedContent->getContent().'-TRANSLATED');
        }

        $replacer = new TextReplacer();
        $content  = $replacer->replace(file_get_contents('./tests/data/pages/case1.html'), $result);

        foreach ($result as $i => $extractedContent) {
            $this->assertStringContainsString($extractedContent->getTranslatedContent(), $content, "It should contain the text: {$extractedContent->getTranslatedContent()}");
        }

        $this->assertStringContainsString('<p>src-TRANSLATED</p>', $content, "It should contain the correct replaced text");
        $this->assertStringContainsString('<p>src="image1.jpg"-TRANSLATED</p>', $content, "It should contain the correct replaced text");
        $this->assertStringContainsString('<img src="image1.jpg-TRANSLATED" ', $content, "It should not replace the src attribute");
        $this->assertStringContainsString('<img src="image2.jpg-TRANSLATED" ', $content, "It should not replace the src attribute");
        $this->assertStringContainsString('<p>paragraph1-TRANSLATED <span>span-TRANSLATED</span> paragraph2-TRANSLATED <i>italic-TRANSLATED</i> paragraph3-TRANSLATED <b>bold-TRANSLATED</b></p>', $content, "It should not replace the src attribute");
    }
}
