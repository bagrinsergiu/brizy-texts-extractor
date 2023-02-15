<?php

declare(strict_types=1);

namespace BrizyTextsExtractor;

use BrizyPlaceholders\ContentPlaceholder;
use BrizyPlaceholders\Extractor;
use BrizyPlaceholders\Registry;

class TextExtractor implements TextExtractorInterface
{
    public const EXCLUDED_TAGS = ['style', 'script'];
    private const DOM_OPTIONS = LIBXML_BIGLINES | LIBXML_NOBLANKS | LIBXML_NONET | LIBXML_NOERROR | LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD | LIBXML_PARSEHUGE;

    /**
     * @param $content
     *
     * @return array<ExtractedContent>
     */
    public function extractFromContent($content): array
    {
        $content = $this->replaceContentPlaceholders($content);

        $dom = new \DOMDocument();
        $dom->loadHTML($content, self::DOM_OPTIONS);

        $result = $this->extractTexts($dom);

        return $result;
    }

    public function extractFromUrl($url): array
    {
        $content = file_get_contents($url);

        if ( ! is_string($content)) {
            return [];
        }

        return $this->extractFromContent($content);
    }

    private function extractTexts($dom, $options = [])
    {
        $defaultOptions = ['excludeTags' => self::EXCLUDED_TAGS];

        $defaultOptions = array_merge($defaultOptions, $options);

        $result = [];
        $xpath  = new \DOMXPath($dom);

        // extract all texts
        foreach ($xpath->query('//text()') as $node) {
            $parent = $node->parentNode;

            if (in_array($parent->tagName, $defaultOptions['excludeTags'])) {
                continue;
            }

            if ($content = trim($node->nodeValue)) {
                $result[] = ExtractedContent::instance($content, ExtractedContent::TYPE_TEXT);
            }
        }

        $result = array_merge($result, $this->extractAttributeTexts($dom, 'placeholder'));
        $result = array_merge($result, $this->extractAttributeStaringWith($dom, 'data-brz-translateble-'));
        $result = array_merge($result, $this->extractImages($dom));

        // remove duplicates
        return array_unique($result);
    }

    private function extractImages($dom)
    {
        $result = [];

        /**
         * @var \DOMElement $pictureNode ;
         */
        // search for sources
        foreach ($dom->getElementsByTagName('source') as $sourceTag) {
            $srcSet = trim($sourceTag->getAttribute('srcset'));
            foreach (explode(',', $srcSet) as $imageSize) {
                $explode = explode(' ', trim($imageSize));
                $src     = $explode[0];

                if ($src) {
                    $result[] = ExtractedContent::instance($src, ExtractedContent::TYPE_MEDIA);
                }
            }
        }

        // extract all img srcs
        foreach ($dom->getElementsByTagName('img') as $node) {

            $srcSet = trim($node->getAttribute('srcset'));

            foreach (explode(',', $srcSet) as $imageSize) {
                $explode = explode(' ', trim($imageSize));
                $src     = $explode[0];
                if ($src) {
                    $result[] = ExtractedContent::instance($src, ExtractedContent::TYPE_MEDIA);
                }
            }

            $src = trim($node->getAttribute('src'));
            $alt = trim($node->getAttribute('alt'));

            if ($src) {
                $result[] = ExtractedContent::instance($src, ExtractedContent::TYPE_MEDIA);
            }

            if ($alt) {
                $result[] = ExtractedContent::instance($alt, ExtractedContent::TYPE_TEXT);
            }
        }

        return $result;
    }

    private function extractAttributeTexts($dom, $attribute)
    {
        $result = [];
        $xpath  = new \DOMXPath($dom);
        foreach ($xpath->query('//*[@'.$attribute.']') as $node) {
            /**
             * @var \DOMNode $node ;
             * @var \DOMNamedNodeMap $t ;
             */

            $attr = $node->attributes->getNamedItem($attribute);
            if ($attr) {
                $result[] = ExtractedContent::instance(trim($attr->value), ExtractedContent::TYPE_TEXT);
            }
        }

        return $result;

    }

    private function extractAttributeStaringWith($dom, $attribute)
    {
        $result = [];
        $xpath  = new \DOMXPath($dom);
        foreach ($xpath->query("//@*[starts-with(name(),'{$attribute}')]") as $nodeAttr) {

            /**
             * @var \DOMAttr $nodeAttr ;
             */
            if ($nodeAttr->value) {
                $result[] = ExtractedContent::instance(trim($nodeAttr->value), ExtractedContent::TYPE_TEXT);
            }
        }

        return $result;
    }


    private function replaceContentPlaceholders($content)
    {
        $extractor = new Extractor(new Registry());
        list($contentPlaceholders, $returnedContent) = $extractor->extractIgnoringRegistry(
            $content,
            function (ContentPlaceholder $p) {
                return  "<div>{$this->replaceContentPlaceholders($p->getContent())}</div>";
            }
        );

        return $returnedContent;
    }
}
