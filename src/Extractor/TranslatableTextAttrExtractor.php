<?php

namespace BrizyTextsExtractor\Extractor;

use BrizyTextsExtractor\Concern\StringUtilsAware;
use BrizyTextsExtractor\DomExtractorInterface;
use BrizyTextsExtractor\ExtractedContent;

class TranslatableTextAttrExtractor implements DomExtractorInterface
{
    use StringUtilsAware;

    const ATTR = 'data-brz-translate-text';

    public function extract(\DOMDocument $document): array
    {
        $result = [];
        $xpath = new \DOMXPath($document);
        foreach ($xpath->query(sprintf("//*[@%s]", self::ATTR)) as $node) {
            /**
             * @var \DOMNode $node ;
             * @var \DOMNamedNodeMap $t ;
             */
            if ($content = $this->cleanString($node->nodeValue)) {
                $result[] = ExtractedContent::instance($content, ExtractedContent::TYPE_TEXT);
            }
        }

        return $result;
    }

}