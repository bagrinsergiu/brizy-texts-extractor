<?php

namespace BrizyTextsExtractor\Extractor;

use BrizyTextsExtractor\Concern\StringUtilsAware;
use BrizyTextsExtractor\DomExtractorInterface;
use BrizyTextsExtractor\ExtractedContent;

class CssImageUrlExtractor implements DomExtractorInterface
{
    use StringUtilsAware;

    public function extract(\DOMDocument $document): array
    {
        $result = [];
        $xpath = new \DOMXPath($document);
        foreach ($xpath->query('//style') as $node) {
            $content = $node->nodeValue;//  attributes->getNamedItem("href");
            if ($content) {
                $matches = [];
                preg_match_all("/background(?:-image)?:\s+url\(\"?(?<url>.*?)\"?\)/im", $content, $matches);
                foreach (array_unique($matches['url']) as $url) {
                    if ($url = $this->cleanString($url))
                        $result[] = ExtractedContent::instance($url, ExtractedContent::TYPE_MEDIA);
                }
            }
        }
        return $result;
    }

}