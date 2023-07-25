<?php

declare(strict_types=1);

namespace BrizyTextsExtractor;

use BrizyPlaceholders\ContentPlaceholder;
use BrizyPlaceholders\Extractor;
use BrizyPlaceholders\Registry;
use function Sabre\Uri\parse;

class TextExtractor implements TextExtractorInterface
{
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


    public function extractSimpleTextsFromContent($content, $options = []): array
    {
        $content = $this->replaceContentPlaceholders($content);

        $dom = new \DOMDocument();
        $dom->loadHTML($content, self::DOM_OPTIONS);

        $result = $this->extractSimpleTexts($dom, $options);

        return $result;
    }

    public function extractBodyTextFromContent($content): array
    {
        $content = $this->replaceContentPlaceholders($content);

        $dom = new \DOMDocument();
        $dom->loadHTML($content, self::DOM_OPTIONS);
        $xpath = new \DOMXPath($dom);

        foreach ($xpath->evaluate("//head|//*[not(normalize-space())]") as $node) {
            $node->remove();
        }

        $link = $dom->createElement('div');
        foreach ($xpath->evaluate("//script|//style|//picture|//img") as $node) {
            $node->parentNode->replaceChild($link, $node);
        }
        return [ExtractedContent::instance($dom->saveHTML($dom->getElementsByTagName('body')->item(0)), ExtractedContent::TYPE_TEXT)];
    }

    public function extractFromUrl($url): array
    {
        $content = file_get_contents($url);

        if (!is_string($content)) {
            return [];
        }

        return $this->extractFromContent($content);
    }

    private function extractSimpleTexts($dom, $options = [])
    {
        $result = [];

        $defaultOptions = [self::EXCLUDED_TAGS => self::EXCLUDED_TAGS_VAL];

        $defaultOptions = array_merge($defaultOptions, $options);

        $result = [];
        $xpath = new \DOMXPath($dom);

        // extract all texts
        foreach ($xpath->query('//head//text()') as $node) {
            $parent = $node->parentNode;

            if (in_array($parent->tagName, $defaultOptions[self::EXCLUDED_TAGS])) {
                continue;
            }

            $content = $this->trim($node->nodeValue);
            if (strlen($content) > 0) {
                $result[] = ExtractedContent::instance($content, ExtractedContent::TYPE_TEXT);
            }
        }


        $result = array_merge($result, $this->extractAttributeTexts($dom, 'placeholder'));
        $result = array_merge($result, $this->extractAttributeStaringWith($dom, 'data-brz-translateble-'));
        $result = array_merge($result, $this->extractImages($dom));
        $result = array_merge($result, $this->extractCssImages($dom, $options));
        return array_unique($result);
    }

    private function extractTextsFromHtmlBody($dom, $options = [])
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

            $content = $this->trim($node->nodeValue);
            if (strlen($content) > 0) {
                $result[] = ExtractedContent::instance($content, ExtractedContent::TYPE_TEXT);
            }
        }

        return array_unique($result);
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
            $content = $this->trim($node->nodeValue);
            if (strlen($content) > 0) {
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

    private function trim($text)
    {
        $trim = trim($text);
        $trim = preg_replace('/^\s*/', "", $trim);
        $trim = preg_replace('/\s*$/', "", $trim);
        return $trim;
    }


    private function extractImages($dom)
    {
        $result = [];

        /**
         * @var \DOMElement $pictureNode ;
         */
        // search for sources
        foreach ($dom->getElementsByTagName('source') as $sourceTag) {
            $srcSet = $this->trim($sourceTag->getAttribute('srcset'));
            foreach (explode(',', $srcSet) as $imageSize) {
                $explode = explode(' ', $this->trim($imageSize));
                $src = $explode[0];

                $src = $this->trim($src);
                if (strlen($src) > 0) {
                    $result[] = ExtractedContent::instance($src, ExtractedContent::TYPE_MEDIA);
                }
            }
        }

        // extract all img srcs
        foreach ($dom->getElementsByTagName('img') as $node) {

            $srcSet = $this->trim($node->getAttribute('srcset'));

            foreach (explode(',', $srcSet) as $imageSize) {
                $explode = explode(' ', $this->trim($imageSize));
                $src = $explode[0];
                $src = $this->trim($src);
                if (strlen($src) > 0) {
                    $result[] = ExtractedContent::instance($src, ExtractedContent::TYPE_MEDIA);
                }
            }

            $src = $this->trim($node->getAttribute('src'));
            $alt = $this->trim($node->getAttribute('alt'));

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
            if ($val = $this->trim($attr->value)) {
                $result[] = ExtractedContent::instance($val, ExtractedContent::TYPE_TEXT);
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
        foreach ($xpath->query('//style') as $node) {
            $content = $node->nodeValue;//  attributes->getNamedItem("href");
            if ($content) {
                $matches = [];
                preg_match_all("/background(?:-image)?:\s+url\(\"?(?<url>.*?)\"?\)/im", $content, $matches);
                foreach (array_unique($matches['url']) as $url) {
                    if ($url = $this->trim($url)) {
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
            if ($val = $this->trim($nodeAttr->value)) {
                $result[] = ExtractedContent::instance($val, ExtractedContent::TYPE_TEXT);
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
