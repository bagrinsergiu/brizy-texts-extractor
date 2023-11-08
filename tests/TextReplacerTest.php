<?php

namespace BrizyTextsExtractorTests;

use BrizyTextsExtractor\ExtractedContent;
use BrizyTextsExtractor\Extractor\BrzTranslatableAttrExtractor;
use BrizyTextsExtractor\Extractor\CssImageUrlExtractor;
use BrizyTextsExtractor\Extractor\ImageUrlExtractor;
use BrizyTextsExtractor\Extractor\OldTextExtractor;
use BrizyTextsExtractor\Extractor\PlaceholderAttrExtractor;
use BrizyTextsExtractor\Extractor\TranslatableMetaContentAttrExtractor;
use BrizyTextsExtractor\TextExtractor;
use BrizyTextsExtractor\TextReplacer;
use PHPUnit\Framework\TestCase;

class TextReplacerTest extends TestCase
{

    public function testReplaceFromContentCase1()
    {
        $domExtractors = [
            new PlaceholderAttrExtractor(),
            new BrzTranslatableAttrExtractor(),
            new TranslatableMetaContentAttrExtractor(),
            new CssImageUrlExtractor(),
            new ImageUrlExtractor(),
            new OldTextExtractor(),
        ];
        $extractor = new TextExtractor($domExtractors);
        $htmlContent = file_get_contents('./tests/data/pages/case1.html.txt');
        $result = $extractor->extractFromContent($htmlContent);

        // add fake translated content
        foreach ($result as $i => $extractedContent) {
            $extractedContent->setTranslatedContent($extractedContent->getContent() . '-TRANSLATED');
        }

        $replacer = new TextReplacer();
        $content = $replacer->replace($htmlContent, $result);

        foreach ($result as $i => $extractedContent) {

            // ignore the images extracted from css urls
            if (strpos($extractedContent->getContent(), "logo-header") !== false)
                continue;

            if (in_array($extractedContent->getContent(), ['placeholder', 'text', 'placeholder text1', 'text2'])) {
                continue;
            }

            $needle = $extractedContent->getTranslatedContent();

            if ($extractedContent->getType() == ExtractedContent::TYPE_MEDIA) {
                $needle = str_replace("&","&amp;", $needle);
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
        $domExtractors = [
            new PlaceholderAttrExtractor(),
            new BrzTranslatableAttrExtractor(),
            new TranslatableMetaContentAttrExtractor(),
            new CssImageUrlExtractor(),
            new ImageUrlExtractor(),
            new OldTextExtractor(),
        ];
        $extractor = new TextExtractor($domExtractors);
        $htmlContent = file_get_contents('./tests/data/pages/case1.html.txt');
        $result = $extractor->extractFromContent($htmlContent);
        $replacer = new TextReplacer();

        $content = $replacer->replace($htmlContent, $result);

        $this->assertStringContainsString('<p>src</p>', $content, 'It should container "src"');
        $this->assertStringContainsString('<p>src="image1.jpg"</p>', $content, 'It should container src="image1.jpg"');
        $this->assertStringContainsString(
            '<p>paragraph1 <span>span</span> paragraph2 <i>italic</i> paragraph3 <b>bold</b></p>',
            $content,
            'It should container "paragraph1"'
        );
        $this->assertStringContainsString(
            '<div data-brz-translatable-label="The website translations1">div1 <div>div2</div>div3</div>',
            $content,
            'It should container "<div data-brz-translatable-label="The website translations1">div1 <div>div2</div>div3</div>"'
        );
        $this->assertStringContainsString('image1.jpg"', $content, 'It should container "image1.jpg"');
        $this->assertStringContainsString('image2.jpg"', $content, 'It should container "image2.jpg"');
        $this->assertStringContainsString('image3.jpg"', $content, 'It should container "image3.jpg"');
        $this->assertStringContainsString('alt_for_image.jpg"', $content, 'It should container "alt_for_image.jpg"');
        $this->assertStringContainsString('next-alt"', $content, 'It should container "next-alt"');
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
        $domExtractors = [
            new PlaceholderAttrExtractor(),
            new BrzTranslatableAttrExtractor(),
            new TranslatableMetaContentAttrExtractor(),
            new CssImageUrlExtractor(),
            new ImageUrlExtractor(),
            new OldTextExtractor(),
        ];
        $extractor = new TextExtractor($domExtractors);
        $htmlContent = file_get_contents('/opt/project/tests/data/pages/case3.html.txt');
        $result = $extractor->extractFromContent($htmlContent);
        $replacer = new TextReplacer();

        $content = $replacer->replace($htmlContent, $result);

        $this->assertStringContainsString('class="brz"', $content, 'It should preserve body class');
    }

   public function testReplaceFromContentCase4()
    {
        $domExtractors = [
            new PlaceholderAttrExtractor(),
            new TranslatableMetaContentAttrExtractor(),
            new BrzTranslatableAttrExtractor(),
            new CssImageUrlExtractor(),
            new ImageUrlExtractor(),
            new OldTextExtractor(),
        ];
        $extractor = new TextExtractor($domExtractors);
        $htmlContent = file_get_contents('/opt/project/tests/data/pages/case4.html.txt');
        $result = $extractor->extractFromContent($htmlContent);

        // add fake translated content
        foreach ($result as $i => $extractedContent) {
            $extractedContent->setTranslatedContent($extractedContent->getContent() . '-TRANSLATED');
        }

        $replacer = new TextReplacer();
        $content = $replacer->replace($htmlContent, $result);

        $this->assertStringContainsString('class="brz"', $content, 'It should preserve body class');
    }
}
