<?php
namespace BrizyTextsExtractor;

interface TextExtractorInterface
{
    /**
     * @param $content
     *
     * @return array<ExtractedContent>
     */
    public function extractFromContent($content): array;

    /**
     * @param $url
     *
     * @return array<ExtractedContent>
     */
    public function extractFromUrl($url): array;
}