<?php

namespace BrizyTextsExtractor\Extractor;

use BrizyTextsExtractor\Concern\StringUtilsAware;
use BrizyTextsExtractor\DomExtractorInterface;
use BrizyTextsExtractor\ExtractedContent;

class BrzTranslatableAttrExtractor implements DomExtractorInterface
{
    use StringUtilsAware;

    const ATTR = 'data-brz-translateble-';

    public function extract(\DOMDocument $document): array
    {
        $result = [];
        $xpath = new \DOMXPath($document);
        foreach ($xpath->query("//@*[starts-with(name(),'" . self::ATTR . "')]") as $nodeAttr) {
            /**
             * @var \DOMAttr $nodeAttr ;
             */
            if ($value = $this->cleanString($nodeAttr->value)) {
                $result[] = ExtractedContent::instance($value, ExtractedContent::TYPE_TEXT);
            }
        }

        return $result;
    }

}