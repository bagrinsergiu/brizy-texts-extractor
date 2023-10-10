<?php

namespace BrizyTextsExtractor\Extractor;

use BrizyTextsExtractor\Concern\StringUtilsAware;
use BrizyTextsExtractor\DomExtractorInterface;
use BrizyTextsExtractor\ExtractedContent;

class TranslatableMetaContentAttrExtractor implements DomExtractorInterface
{
    use StringUtilsAware;

    const ATTR = 'content';

    public function extract(\DOMDocument $document): array
    {
        $includeNameMetaNames = ['keywords', 'description','twitter:title'];
        $includePropertyMetaNames = ['og:url', 'og:title', 'og:description', 'og:image', 'og:video', 'og:audio','twitter:title'];

        $result = [];
        $xpath = new \DOMXPath($document);

        $getMetaTextType = function ($name) {
            switch ($name) {
                case 'twitter:image':
                case 'og:image':
                    return ExtractedContent::TYPE_MEDIA;
                default:
                    return ExtractedContent::TYPE_TEXT;
            }
        };

        foreach ($xpath->query('//meta') as $node) {
            /**
             * @var \DOMNode $node ;
             * @var \DOMNamedNodeMap $t ;
             */
            $nameAttr = $node->attributes->getNamedItem('name');
            $name = $nameAttr ? $this->cleanString($nameAttr->value) : '';

            if ($name) {
                if (!in_array($name, $includeNameMetaNames)) continue;
            } else {
                $propertyAttr = $node->attributes->getNamedItem('property');
                $name = $propertyAttr ? $this->cleanString($propertyAttr->value) : '';
                if (!in_array($name, $includePropertyMetaNames)) continue;
            }

            $contentAttr = $node->attributes->getNamedItem('content');
            $contentAttrValue = $contentAttr ? $this->cleanString($contentAttr->value) : '';

            if (($val = strip_tags($contentAttrValue)) && $name) {
                $result[] = ExtractedContent::instance($val, $getMetaTextType($name));
            }
        }

        return $result;
    }

}