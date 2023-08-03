<?php

namespace BrizyTextsExtractor\Extractor;

use BrizyTextsExtractor\Concern\StringUtilsAware;
use BrizyTextsExtractor\DomExtractorInterface;
use BrizyTextsExtractor\ExtractedContent;

class PlaceholderAttrExtractor implements DomExtractorInterface
{
    use StringUtilsAware;

    const ATTR = 'placeholder';

    public function extract(\DOMDocument $document): array
    {
        $result = [];
        $xpath = new \DOMXPath($document);
        foreach ($xpath->query(sprintf("//*[@%s]", self::ATTR)) as $node) {
            /**
             * @var \DOMNode $node ;
             * @var \DOMNamedNodeMap $t ;
             */
            $attr = $node->attributes->getNamedItem(self::ATTR);
            if ($attr) {
                if ($value = $this->cleanString($attr->value)) {
                    $result[] = ExtractedContent::instance($value, ExtractedContent::TYPE_TEXT);
                }
            }
        }

        return $result;
    }

}