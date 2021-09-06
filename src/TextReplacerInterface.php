<?php
namespace BrizyTextsExtractor;

interface TextReplacerInterface
{
    /**
     * @param string $content
     * @param array<ExtractedContent> $extractedContents
     *
     * @return string
     */
    public function replace(string $content, array $extractedContents): string;
}