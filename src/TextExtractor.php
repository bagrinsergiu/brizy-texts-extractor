<?php

declare(strict_types=1);

namespace BrizyTextsExtractor;

use BrizyPlaceholders\ContentPlaceholder;
use BrizyPlaceholders\Extractor;
use BrizyPlaceholders\Registry;
use function Sabre\Uri\parse;

class TextExtractor implements TextExtractorInterface
{
    public const URL_INFO = 'urlInfo';
    public const EXCLUDED_TAGS = 'excludeTags';

    public const EXCLUDED_TAGS_VAL = ['style', 'script'];
    private const DOM_OPTIONS = LIBXML_BIGLINES | LIBXML_NOBLANKS | LIBXML_NONET | LIBXML_NOERROR | LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD | LIBXML_PARSEHUGE;

    /**
     * @param $content
     *
     * @return array<ExtractedContent>
     */
    public function extractFromContent($content, $options = []): array
    {
        $content = $this->replaceContentPlaceholders($content);

        $dom = new \DOMDocument();
        $dom->loadHTML($content, self::DOM_OPTIONS);

        $result = $this->extractTexts($dom, $options);

        return $result;
    }

    public function extractFromUrl($url): array
    {
        $content = file_get_contents($url);

        $urlParsed = parse($url);

        if (!is_string($content)) {
            return [];
        }

        return $this->extractFromContent($content, [self::URL_INFO => $urlParsed]);
    }

    private function extractTexts($dom, $options = [])
    {
        $defaultOptions = [self::EXCLUDED_TAGS => self::EXCLUDED_TAGS_VAL];

        $defaultOptions = array_merge($defaultOptions, $options);

        $result = [];
        $xpath = new \DOMXPath($dom);

        // extract all texts
        foreach ($xpath->query('//text()') as $node) {
            $parent = $node->parentNode;

            if (in_array($parent->tagName, $defaultOptions[self::EXCLUDED_TAGS])) {
                continue;
            }

            if ($content = trim($node->nodeValue)) {
                $result[] = ExtractedContent::instance($content, ExtractedContent::TYPE_TEXT);
            }
        }

        $result = array_merge($result, $this->extractAttributeTexts($dom, 'placeholder'));
        $result = array_merge($result, $this->extractAttributeStaringWith($dom, 'data-brz-translateble-'));
        $result = array_merge($result, $this->extractImages($dom));
        $result = array_merge($result, $this->extractCssImages($dom, $options));

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
                $src = $explode[0];

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
                $src = $explode[0];
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
        $xpath = new \DOMXPath($dom);
        foreach ($xpath->query('//*[@' . $attribute . ']') as $node) {
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

    private function hasTheSameHost($url, $options)
    {
        $urlData = parse($url);

        // return true as this is a relative path without domain
        if ($urlData['host'] == "")
            return true;

        // return true as the page url info was no provided
        if (!isset($options[self::URL_INFO])) {
            return true;
        }

        // return true as the host matches
        if ($options[self::URL_INFO] == $urlData['host']) {
            return true;
        }

        return false;
    }

    private function extractCssImages($dom, $options = [])
    {
        $result = [];
        $xpath = new \DOMXPath($dom);
        foreach ($xpath->query('//link[@rel="stylesheet"][@type="text/css"]') as $node) {
            $attr = $node->attributes->getNamedItem("href");
            if ($attr && $attr->value) {
                if (!$this->hasTheSameHost($attr->value, $options))
                    continue;

                $content = @file_get_contents($attr->value);
                if ($content) {
                    $matches = [];
                    preg_match_all("/background(?:-image)?:\s+url\(\"?(?<url>.*?)\"?\)/im", $content, $matches);

                    foreach (array_unique($matches['url']) as $url) {

                        $url = trim($url);
                        if (!$this->hasTheSameHost($url, $options))
                            continue;

                        $result[] = ExtractedContent::instance($url, ExtractedContent::TYPE_MEDIA);
                    }
                }
            }
        }
        return $result;
    }

    private function extractAttributeStaringWith($dom, $attribute)
    {
        $result = [];
        $xpath = new \DOMXPath($dom);
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
                return "<div>{$this->replaceContentPlaceholders($p->getContent())}</div>";
            }
        );

        return $returnedContent;
    }
}
