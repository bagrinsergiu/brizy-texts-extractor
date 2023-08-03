<?php

declare(strict_types=1);

namespace BrizyTextsExtractor;

use BrizyPlaceholders\ContentPlaceholder;
use BrizyPlaceholders\Extractor;
use BrizyPlaceholders\Registry;
use function Sabre\Uri\parse;

class TextExtractor implements TextExtractorInterface
{
    private const DOM_OPTIONS = LIBXML_BIGLINES | LIBXML_NOBLANKS | LIBXML_NONET | LIBXML_NOERROR | LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD | LIBXML_PARSEHUGE;

    /**
     * @var array<DomExtractorInterface>
     */
    private $extractors = [];

    public function __construct(array $extractors = [])
    {
        foreach ($extractors as $extractor) {
            $this->extractors[get_class($extractor)] = $extractor;
        }
    }

    /**
     * @return DomExtractorInterface[]
     */
    public function getExtractors(): array
    {
        return $this->extractors;
    }

    /**
     * @param DomExtractorInterface[] $extractors
     */
    public function setExtractors(array $extractors): TextExtractorInterface
    {
        $this->extractors = $extractors;

        return $this;
    }

    /**
     * @param DomExtractorInterface $extractor
     */
    public function addExtractor(DomExtractorInterface $extractor): TextExtractorInterface
    {
        $this->extractors[get_class($extractor)] = $extractor;
        return $this;
    }

    /**
     * @param DomExtractorInterface $extractor
     */
    public function removeExtractor(string $extractorClass): TextExtractorInterface
    {
        unset($this->extractors[$extractorClass]);
        return $this;
    }

    /**
     * @param $content
     *
     * @return array<ExtractedContent>
     */
    public function extractFromContent($content, $options = []): array
    {
        $content = $this->replaceContentPlaceholders($content);

        $dom = new \DOMDocument();
        $dom->loadHTML($content, self::DOM_OPTIONS);

        $result = $this->extractTexts($dom, $options);

        return $result;
    }

    public function extractFromUrl($url): array
    {
        $content = file_get_contents($url);

        if (!is_string($content)) {
            return [];
        }

        return $this->extractFromContent($content);
    }

    private function extractTexts($dom, $options = [])
    {
        $result = [];
        foreach ($this->extractors as $extractor) {
            $result = array_merge($result, $extractor->extract($dom));
        }

        return array_unique($result);
    }

    private function replaceContentPlaceholders($content)
    {
        $extractor = new Extractor(new Registry());
        list($contentPlaceholders, $returnedContent) = $extractor->extractIgnoringRegistry(
            $content,
            function (ContentPlaceholder $p) {
                return "<div>{$this->replaceContentPlaceholders($p->getContent())}</div>";
            }
        );

        return $returnedContent;
    }
}
