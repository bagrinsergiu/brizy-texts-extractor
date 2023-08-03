<?php

namespace BrizyTextsExtractor\Extractor;

use BrizyTextsExtractor\Concern\StringUtilsAware;
use BrizyTextsExtractor\DomExtractorInterface;
use BrizyTextsExtractor\ExtractedContent;

class OldTextExtractor implements DomExtractorInterface
{
    use StringUtilsAware;

    const EXCLUDED_TAGS_KEY = 'excludeTags';

    const EXCLUDED_TAGS_VAL = ['style', 'script'];

    const ATTR = 'data-brz-translate-text';

    public function extract(\DOMDocument $document): array
    {
        $defaultOptions = [self::EXCLUDED_TAGS_KEY => self::EXCLUDED_TAGS_VAL];

        $result = [];
        $xpath = new \DOMXPath($document);

        // extract all texts
        foreach ($xpath->query('//text()') as $node) {
            $parent = $node->parentNode;

            if (in_array($parent->tagName, $defaultOptions[self::EXCLUDED_TAGS_KEY])) {
                continue;
            }

            if ($content = $this->cleanString($node->nodeValue)) {
                $result[] = ExtractedContent::instance($content, ExtractedContent::TYPE_TEXT);
            }
        }
        return $result;
    }
}