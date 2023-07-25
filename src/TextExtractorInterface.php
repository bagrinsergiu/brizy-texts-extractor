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
     * This should extract all texts from html string but only texts from attributes.. images.. and texts that are not splited by tags
     *
     * @param $content
     * @return array<ExtractedContent>
     */
    public function extractSimpleTextsFromContent($content): array;

    public function extractBodyTextFromContent($content): array;

    /**
     * @param $url
     *
     * @return array<ExtractedContent>
     */
    public function extractFromUrl($url): array;
}