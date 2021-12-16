<?php

declare(strict_types=1);

namespace BrizyTextsExtractor;

class TextExtractor implements TextExtractorInterface
{
    public const EXCLUDED_TAGS = ['style', 'script'];

    /**
     * @param $content
     *
     * @return array<ExtractedContent>
     */
    public function extractFromContent($content): array
    {
        $result = [];

        $dom = new \DOMDocument();
        $dom->loadHTML(
            $content,
            LIBXML_BIGLINES | LIBXML_NOBLANKS | LIBXML_NONET | LIBXML_NOERROR | LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
        );

        $result = $this->extractTexts($dom);

        return $result;
    }

    public function extractFromUrl($url): array
    {
        $content = file_get_contents($url);

        if (!is_string($content)) {
            return [];
        }

        return $this->extractFromContent($content);
    }

    private function extractTexts($dom)
    {
        $result = [];
        $xpath = new \DOMXPath($dom);

        // extract all texts
        foreach ($xpath->query('//text()') as $node) {
            $parent = $node->parentNode;

            if (in_array($parent->tagName, self::EXCLUDED_TAGS)) {
                continue;
            }

            if ($content = trim($node->nodeValue)) {
                $result[] = ExtractedContent::instance($content, ExtractedContent::TYPE_TEXT);
            }
        }

        $result = array_merge($result, $this->extractImages($dom));

        // remove duplicates
        return array_unique($result);
    }

    private function extractImages($dom)
    {
        $result = [];

        // extract all picture nodes
        foreach ($dom->getElementsByTagName('picture') as $pictureNode) {
            /**
             * @var \DOMElement $pictureNode ;
             */
            // search for sources
            foreach ($pictureNode->getElementsByTagName('source') as $sourceTag) {
                $srcSet = trim($sourceTag->getAttribute('srcset'));
                foreach (explode(',', $srcSet) as $imageSize) {
                    $explode = explode(' ', trim($imageSize));
                    $src = $explode[0];

                    $result[] = ExtractedContent::instance($src, ExtractedContent::TYPE_MEDIA);
                }
            }

            // search for img tags
            foreach ($pictureNode->getElementsByTagName('img') as $imgTag) {
                $srcSet = trim($imgTag->getAttribute('srcset'));

                foreach (explode(',', $srcSet) as $imageSize) {
                    $explode = explode(' ', trim($imageSize));
                    $src = $explode[0];
                    $result[] = ExtractedContent::instance($src, ExtractedContent::TYPE_MEDIA);
                }

                $src = trim($imgTag->getAttribute('src'));
                $alt = trim($imgTag->getAttribute('alt'));

                if ($src) {
                    $result[] = ExtractedContent::instance($src, ExtractedContent::TYPE_MEDIA);
                }

                if ($alt) {
                    $result[] = ExtractedContent::instance($alt, ExtractedContent::TYPE_IMAGE_ALT);
                }
            }
        }

        // remove all picture nodes for to avoid future img search
        foreach ($dom->getElementsByTagName('picture') as $pictureNode) {
            $pictureNode->parentNode->removeChild($pictureNode);
        }

        // extract all img srcs
        foreach ($dom->getElementsByTagName('img') as $node) {
            $src = trim($node->getAttribute('src'));
            $alt = trim($node->getAttribute('alt'));

            if ($src) {
                $result[] = ExtractedContent::instance($src, ExtractedContent::TYPE_MEDIA);
            }

            if ($alt) {
                $result[] = ExtractedContent::instance($alt, ExtractedContent::TYPE_IMAGE_ALT);
            }
        }

        return $result;
    }
}
