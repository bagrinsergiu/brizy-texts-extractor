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


    public function getExtractors(): array;

    /**
     * @param DomExtractorInterface[] $extractors
     */
    public function setExtractors(array $extractors): TextExtractorInterface;

    /**
     * @param DomExtractorInterface $extractor
     */
    public function addExtractor(DomExtractorInterface $extractor): TextExtractorInterface;

    /**
     * @param DomExtractorInterface $extractor
     */
    public function removeExtractor(string $extractorClass): TextExtractorInterface;

}