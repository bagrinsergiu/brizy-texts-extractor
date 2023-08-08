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
            foreach (explode(',', $srcSet) as $imageSize) {
                $explode = explode(' ', trim($imageSize));
                $src = null;
                if (count($explode) > 2) {
                    unset($explode[count($explode) - 1]);
                    $src = implode(' ', $explode);
                } else {
                    $src = $explode[0];
                }


                if ($src) {
                    $result[] = ExtractedContent::instance($src, ExtractedContent::TYPE_MEDIA);
                }
            }
        }

        // extract all img srcs
        foreach ($document->getElementsByTagName('img') as $node) {

            $srcSet = trim($node->getAttribute('srcset'));

            foreach (explode(',', $srcSet) as $imageSize) {
                $explode = explode(' ', trim($imageSize));
                $src = null;
                if (count($explode) > 2) {
                    unset($explode[count($explode) - 1]);
                    $src = implode(' ', $explode);
                } else {
                    $src = $explode[0];
                }


                if ($src) {
                    $result[] = ExtractedContent::instance($src, ExtractedContent::TYPE_MEDIA);
                }
            }

            $src = $this->cleanString($node->getAttribute('src'));
            $alt = $this->cleanString($node->getAttribute('alt'));

            if ($src) {
                $result[] = ExtractedContent::instance($src, ExtractedContent::TYPE_MEDIA);
            }

            if ($alt) {
                $result[] = ExtractedContent::instance($alt, ExtractedContent::TYPE_TEXT);
            }
        }

        return $result;
    }

}