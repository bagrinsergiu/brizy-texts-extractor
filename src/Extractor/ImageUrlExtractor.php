<?php

namespace BrizyTextsExtractor\Extractor;

use BrizyTextsExtractor\Concern\StringUtilsAware;
use BrizyTextsExtractor\DomExtractorInterface;
use BrizyTextsExtractor\ExtractedContent;

class ImageUrlExtractor implements DomExtractorInterface
{
    use StringUtilsAware;

    public function extract(\DOMDocument $document): array
    {
        $result = [];

        /**
         * @var \DOMElement $pictureNode ;
         */
        // search for sources
        foreach ($document->getElementsByTagName('source') as $sourceTag) {
            $srcSet = trim($sourceTag->getAttribute('srcset'));
             if (empty($srcSet)) {
                continue;
            }
            foreach (explode(',', $srcSet) as $imageSize) {
                $srcT = $this->extractImageFromSizeValue($this->trim($imageSize));
                if (!empty($srcT)) {
                    $result[] = ExtractedContent::instance($srcT, ExtractedContent::TYPE_MEDIA);
                }
            }
        }

        // extract all img srcs
        foreach ($document->getElementsByTagName('img') as $node) {

            $srcSet = trim($node->getAttribute('srcset'));

             if (!empty($srcSet)) {
                foreach (explode(',', $srcSet) as $imageSize) {
                    $srcT = $this->extractImageFromSizeValue($this->trim($imageSize));
                    if (!empty($srcT)) {
                        $result[] = ExtractedContent::instance($srcT, ExtractedContent::TYPE_MEDIA);
                    }
                }
            }

            $src = $this->cleanString($node->getAttribute('src'));
            $alt = $this->cleanString($node->getAttribute('alt'));

           if ($src && strpos($src, 'data:image') === false) {
                $result[] = ExtractedContent::instance($src, ExtractedContent::TYPE_MEDIA);
            }

            if ($alt) {
                $result[] = ExtractedContent::instance($alt, ExtractedContent::TYPE_TEXT);
            }
        }

        return $result;
    }

    private function extractImageFromSizeValue($value)
    {
        // ignore the inline images.
        if (strpos($value, 'data:image') !== false) {
            return null;
        }

        $explode = explode(',', $value);
        if (count($explode) == 0) {
            return $value;
        }

        foreach ($explode as $imageSize) {
            $imageSize = $this->trim($imageSize);
            $spaceIndex = strrpos($imageSize, ' ');
            if ($spaceIndex === false) {
                return $imageSize;
            }

            $imageUrl = substr($imageSize, 0, $spaceIndex);
            $imageUrl = $this->trim($imageUrl);
            if (strlen($imageUrl) > 0) {
                return $imageUrl;
            }
        }

        return null;
    }


}