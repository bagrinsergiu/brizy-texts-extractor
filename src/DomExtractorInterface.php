<?php

namespace BrizyTextsExtractor;

interface DomExtractorInterface
{
    /***
     * @param \DOMDocument $document
     * @return array<ExtractedContent>
     */
    public function extract(\DOMDocument $document): array;
}