<?php

namespace BrizyTextsExtractorTests;

use BrizyTextsExtractor\ExtractedContent;
use BrizyTextsExtractor\TextExtractor;
use BrizyTextsExtractor\TextReplacer;
use PHPUnit\Framework\TestCase;

class TextReplacerTest extends TestCase
{
    public function testReplaceFromContentCase1()
    {
        $extractor = new TextExtractor();
        $htmlContent = file_get_contents('/opt/project/tests/data/pages/case1.html');
        $result = $extractor->extractFromContent($htmlContent);

        // add fake translated content
        foreach ($result as $i => $extractedContent) {
            $extractedContent->setTranslatedContent($extractedContent->getContent() . '-TRANSLATED');
        }

        $replacer = new TextReplacer();
        $content = $replacer->replace($htmlContent, $result);

        foreach ($result as $i => $extractedContent) {

            // ignore the images extracted from css urls
            if (strpos($extractedContent->getContent(), "logo-header") !== false) {
                continue;
            }

            if (in_array($extractedContent->getContent(), ['placeholder', 'text', 'placeholder text1', 'text2'])) {
                continue;
            }

            $needle = $extractedContent->getTranslatedContent();

            if ($extractedContent->getType() == ExtractedContent::TYPE_MEDIA) {
                $needle = str_replace("&", "&amp;", $needle);
            }

            $this->assertStringContainsString(
                $needle,
                $content,
                "It should contain the text: {$needle}"
            );
        }

        $this->assertStringContainsString(
            '<p>src-TRANSLATED</p>',
            $content,
            "It should contain the correct replaced text"
        );
        $this->assertStringContainsString(
            '<p>src="image1.jpg"-TRANSLATED</p>',
            $content,
            "It should contain the correct replaced text"
        );
        $this->assertStringContainsString(
            '<img src="image1.jpg-TRANSLATED" ',
            $content,
            "It should not replace the src attribute"
        );
        $this->assertStringContainsString(
            '<img src="image2.jpg-TRANSLATED" ',
            $content,
            "It should not replace the src attribute"
        );
        $this->assertStringContainsString(
            '<p>paragraph1-TRANSLATED <span>span-TRANSLATED</span> paragraph2-TRANSLATED <i>italic-TRANSLATED</i> paragraph3-TRANSLATED <b>bold-TRANSLATED</b></p>',
            $content,
            "It should not replace the src attribute"
        );
        $this->assertStringContainsString(
            'placeholder="placeholder-TRANSLATED"',
            $content,
            "It should not replace the placeholder attribute"
        );

    }

    public function testReplaceFromContentCase2()
    {
        $extractor = new TextExtractor();
        $htmlContent = file_get_contents('/opt/project/tests/data/pages/case1.html');
        $result = $extractor->extractFromContent($htmlContent);
        $replacer = new TextReplacer();

        $content = $replacer->replace($htmlContent, $result);

        $this->assertStringContainsString('<p>src</p>', $content, 'It should contain "src"');
        $this->assertStringContainsString('<p>src="image1.jpg"</p>', $content, 'It should contain src="image1.jpg"');
        $this->assertStringContainsString(
            '<p>paragraph1 <span>span</span> paragraph2 <i>italic</i> paragraph3 <b>bold</b></p>',
            $content,
            'It should contain "paragraph1"'
        );
        $this->assertStringContainsString(
            '<div data-brz-translatable-label="The website translations1">div1 <div>div2</div>div3</div>',
            $content,
            'It should contain "<div data-brz-translateble-label="The website translations1">div1 <div>div2</div>div3</div>"'
        );
        $this->assertStringContainsString('image1.jpg"', $content, 'It should contain "image1.jpg"');
        $this->assertStringContainsString('image2.jpg"', $content, 'It should contain "image2.jpg"');
        $this->assertStringContainsString('image3.jpg"', $content, 'It should contain "image3.jpg"');
        $this->assertStringContainsString('alt_for_image.jpg"', $content, 'It should contain "alt_for_image.jpg"');
        $this->assertStringContainsString('next-alt"', $content, 'It should contain "next-alt"');
        $this->assertStringContainsString(
            'brizy-wp-pricing-1.jpg ',
            $content,
            'It should contain "brizy-wp-pricing-1.jpg"'
        );
        $this->assertStringContainsString(
            'brizy-wp-pricing-2.jpg ',
            $content,
            'It should contain "brizy-wp-pricing-2.jpg"'
        );
        $this->assertStringContainsString(
            'brizy-wp-pricing-11.jpg ',
            $content,
            'It should contain "brizy-wp-pricing-11.jpg"'
        );
        $this->assertStringContainsString(
            'brizy-wp-pricing-22.jpg ',
            $content,
            'It should contain "brizy-wp-pricing-22.jpg"'
        );
        $this->assertStringContainsString(
            'brizy-wp-pricing-13.jpg"',
            $content,
            'It should contain "brizy-wp-pricing-13.jpg"'
        );
        $this->assertStringContainsString('brizy.jpg"', $content, 'It should contain "brizy.jpg"');
        $this->assertStringContainsString(
            'NOT INCLUDE STYLE FROM HEAD<',
            $content,
            'It should contain "NOT INCLUDE STYLE FROM HEAD"'
        );
        $this->assertStringContainsString(
            'NOT INCLUDE SCRIPT FROM HEAD<',
            $content,
            'It should contain "NOT INCLUDE SCRIPT FROM HEAD"'
        );
        $this->assertStringContainsString(
            'NOT INCLUDE STYLE FROM HEAD<',
            $content,
            'It should contain "NOT INCLUDE STYLE FROM BODY"'
        );
        $this->assertStringContainsString(
            'NOT INCLUDE SCRIPT FROM HEAD<',
            $content,
            'It should contain "NOT INCLUDE SCRIPT FROM BODY"'
        );
    }

    public function testReplaceFromContentCase3()
    {
        $extractor = new TextExtractor();
        $htmlContent = file_get_contents('/opt/project/tests/data/pages/case3.html');
        $result = $extractor->extractFromContent($htmlContent);
        $replacer = new TextReplacer();

        $content = $replacer->replace($htmlContent, $result);

        $this->assertStringContainsString('class="brz"', $content, 'It should preserve body class');
    }

    public function testReplaceFromContentCase4()
    {
        $extractor = new TextExtractor();
        $htmlContent = file_get_contents('/opt/project/tests/data/pages/case4.html');
        $result = $extractor->extractFromContent($htmlContent);

        // add fake translated content
        foreach ($result as $i => $extractedContent) {
            $extractedContent->setTranslatedContent($extractedContent->getContent().'-TRANSLATED');
        }

        $replacer = new TextReplacer();
        $content = $replacer->replace($htmlContent, $result);

        $this->assertStringContainsString('class="brz"', $content, 'It should preserve body class');
    }

    public function testMetaFromContent()
    {
        $extractor = new TextExtractor();
        $contents = file_get_contents('/opt/project/tests/data/pages/meta.html');
        $result = $extractor->extractFromContent($contents);

        // add fake translated content
        foreach ($result as $i => $extractedContent) {
            $extractedContent->setTranslatedContent($extractedContent->getContent().'-TRANSLATED');
        }

        $replacer = new TextReplacer();
        $content = $replacer->replace($contents, $result);


        $this->assertCount(6, $result, 'It should return the correct count of texts');

        $this->assertFalse(in_array('IGNORE', $result), 'It should contain "IGNORE"');
        $this->assertFalse(in_array('twitter:card', $result), 'It should contain "twitter:card"');
        $this->assertFalse(in_array('og:type', $result), 'It should contain "og:type"');

        $this->assertTrue(in_array('og:site_name', $result), 'It should contain "og:site_name"');
        $this->assertTrue(in_array('og:title', $result), 'It should contain "og:title"');
        $this->assertTrue(in_array('og:description', $result), 'It should contain "og:description"');
        $this->assertTrue(in_array('og:url', $result), 'It should contain "og:url"');
        $this->assertTrue(in_array('og:image', $result), 'It should contain "og:image"');
    }

     public function testCustomerHtmlTest()
    {
        $extractor = new TextExtractor();
        $html = htmlspecialchars_decode(file_get_contents('/opt/project/tests/data/pages/case9.html'));
        $result = $extractor->extractFromContent($html);

        foreach ($result as $text) {

            // strip the brizy media url
            if (ExtractedContent::TYPE_MEDIA == $text->getType()) {
                $text->setContent(preg_replace("/^https:\/\/test-beta1\.b-cdn\.net\/media\/.*?\/(.*)$/", '$1', $text->getContent()));
            }
            $text->setTranslatedContent($text->getContent()."-translated");
        }

        $replacer = new TextReplacer();
        $content = $replacer->replace($html, $result);

        $this->assertStringContainsString('src="https://test-beta1.b-cdn.net/media/original/5650f2dd9af90867232d3310ec4e9100/icon.svg-translated"', $content, 'It should contain the translated image');
    }
}

