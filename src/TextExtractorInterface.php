<?php
namespace BrizyTextsExtractor;

interface TextExtractorInterface
{
    public function extractFromContent($content): array;

    public function extractFromUrl($url): array;
}