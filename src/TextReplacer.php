<?php

declare(strict_types=1);

namespace BrizyTextsExtractor;

class TextReplacer implements TextReplacerInterface
{
    public function replace(string $content, array $extractedContents): string
    {
        list($replace, $with) = $this->returnStrReplaceArguments($extractedContents);

        $content = str_replace($replace, $with, $content);

        return $content;
    }

    /**
     * @param array<ExtractedContent> $extractedContents
     */
    private function returnStrReplaceArguments(array $extractedContents)
    {
        $replace = [];
        $with = [];
        foreach ($extractedContents as $extractedContent) {
            if ($extractedContent->getTranslatedContent()) {
                $replace[] = $extractedContent->getContent();
                $with[] = $extractedContent->getTranslatedContent();
            }
        }

        return [$replace, $with];
    }
}
