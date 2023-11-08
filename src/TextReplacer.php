<?php

declare(strict_types=1);

namespace BrizyTextsExtractor;

class TextReplacer implements TextReplacerInterface
{
    public const EXCLUDED_TAGS = ['style', 'script'];

    public function replace(string $content, array $translatedContents, $options = []): string
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->formatOutput = false;
        $dom->loadHTML(
            $content,
            LIBXML_BIGLINES | LIBXML_HTML_NODEFDTD | LIBXML_NOERROR | LIBXML_NOWARNING | LIBXML_NOXMLDECL | LIBXML_PARSEHUGE | LIBXML_HTML_NOIMPLIED
        );

        $defaultOptions = ['excludeTags' => self::EXCLUDED_TAGS];
        $defaultOptions = array_merge($defaultOptions, $options);

        /**
         * @var array<ExtractedContent> $translatedTexts ;
         * @var array<ExtractedContent> $translatedMedias ;
         */
        $translatedTexts = [];
        $translatedMedias = [];
        foreach ($translatedContents as $translatedContent) {
            $md5Hash = md5($translatedContent->getContent());
            if ($translatedContent->getType() === ExtractedContent::TYPE_MEDIA) {
                $translatedMedias[$md5Hash] = $translatedContent;
            } else {
                $translatedTexts[$md5Hash] = $translatedContent;
            }
        }

        $xpath = new \DOMXPath($dom);

        // extract all texts
        foreach ($xpath->query('//text()') as $node) {
            /**
             * @var \DOMNode $node ;
             * @var \DOMNode $parent ;
             */
            $parent = $node->parentNode;

            if (in_array($parent->tagName, $defaultOptions['excludeTags'])) {
                continue;
            }

            if ($string = trim($node->nodeValue)) {
                $md5NodeValue = md5($string);
                if (isset($translatedTexts[$md5NodeValue]) && $translatedTexts[$md5NodeValue]->getTranslatedContent()) {
                    $node->nodeValue = str_replace(
                        $translatedTexts[$md5NodeValue]->getContent(),
                        $translatedTexts[$md5NodeValue]->getTranslatedContent(),
                        $node->nodeValue
                    );
                }
            }

        }

        foreach ($xpath->query('//*[@placeholder]') as $node) {
            /**
             * @var \DOMNode $node ;
             */

            $attr = $node->attributes->getNamedItem('placeholder');
            if ($attr) {
                $content = $attr->value;
                $md5NodeValue = md5($content);
                if (isset($translatedTexts[$md5NodeValue]) && $translatedTexts[$md5NodeValue]->getTranslatedContent()) {
                    $node->attributes->getNamedItem('placeholder')->value = str_replace(
                        $translatedTexts[$md5NodeValue]->getContent(),
                        $translatedTexts[$md5NodeValue]->getTranslatedContent(),
                        $content
                    );
                }
            }
        }

        $attribute = "data-brz-translatable-";
        foreach ($xpath->query("//@*[starts-with(name(),'{$attribute}')]") as $nodeAttr) {
            /**
             * @var \DOMNode $node ;
             */

            $content = $nodeAttr->value;
            $md5NodeValue = md5($content);
            if (isset($translatedTexts[$md5NodeValue]) && $translatedTexts[$md5NodeValue]->getTranslatedContent()) {
                $nodeAttr->value = str_replace(
                    $translatedTexts[$md5NodeValue]->getContent(),
                    $translatedTexts[$md5NodeValue]->getTranslatedContent(),
                    $content
                );
            }
        }

        /**
         * @var \DOMElement $pictureNode ;
         */
        // search for sources
        foreach ($dom->getElementsByTagName('source') as $sourceTag) {
            $srcSet = trim($sourceTag->getAttribute('srcset'));

            if ($srcSet) {
                foreach ($translatedMedias as $media) {
                    if (strpos($srcSet, $media->getContent()) !== false && $media->getTranslatedContent()) {
                        $srcSet = str_replace(
                            $media->getContent(),
                            $media->getTranslatedContent(),
                            $srcSet
                        );
                        $sourceTag->setAttribute('srcset', $srcSet);
                    }
                }
            }
        }

        // search img srcs
        foreach ($dom->getElementsByTagName('img') as $node) {
            $srcSet = trim($node->getAttribute('srcset'));
            $src = trim($node->getAttribute('src'));
            $alt = trim($node->getAttribute('alt'));

            $md5Src = md5($src);
            $md5Alt = md5($alt);

            if ($srcSet) {
                foreach ($translatedMedias as $media) {
                    if (strpos($srcSet, $media->getContent()) !== false && $media->getTranslatedContent()) {
                        $srcSet = str_replace(
                            $media->getContent(),
                            $media->getTranslatedContent(),
                            $srcSet
                        );
                        $node->setAttribute('srcset', $srcSet);
                    }
                }
            }

            if ($src && isset($translatedMedias[$md5Src]) && $translatedMedias[$md5Src]->getTranslatedContent()) {
                $node->setAttribute(
                    'src',
                    str_replace(
                        $translatedMedias[$md5Src]->getContent(),
                        $translatedMedias[$md5Src]->getTranslatedContent(),
                        $src
                    )
                );
            }

            if ($alt && isset($translatedTexts[$md5Alt]) && $translatedTexts[$md5Alt]->getTranslatedContent()) {
                $node->setAttribute(
                    'alt',
                    str_replace(
                        $translatedTexts[$md5Alt]->getContent(),
                        $translatedTexts[$md5Alt]->getTranslatedContent(),
                        $alt
                    )
                );
            }
        }

        // search meta tag values
        $includeNameMetaNames = ['keywords', 'description'];
        $includePropertyMetaNames = ['og:url', 'og:title', 'og:description', 'og:image', 'og:video', 'og:audio'];
        foreach ($xpath->query('//meta') as $node) {
            /**
             * @var \DOMNode $node ;
             * @var \DOMNamedNodeMap $t ;
             */
            $nameAttr = $node->attributes->getNamedItem('name');
            $name = $nameAttr ? trim($nameAttr->value) : '';

            if ($name) {
                if (!in_array($name, $includeNameMetaNames)) continue;
            } else {
                $propertyAttr = $node->attributes->getNamedItem('property');
                $name = $propertyAttr ? trim($propertyAttr->value) : '';
                if (!in_array($name, $includePropertyMetaNames)) continue;
            }

            $contentAttr = $node->attributes->getNamedItem('content');
            $contentAttrValue = $contentAttr ? $contentAttr->value : '';
            $md5NodeValue = md5($contentAttrValue);

            $searchVal = isset($translatedTexts[$md5NodeValue]) ? $translatedTexts[$md5NodeValue]->getContent() : (isset($translatedMedias[$md5NodeValue]) ? $translatedMedias[$md5NodeValue]->getContent() : '');
            $replaceVal = isset($translatedTexts[$md5NodeValue]) ? $translatedTexts[$md5NodeValue]->getTranslatedContent() : (isset($translatedMedias[$md5NodeValue]) ? $translatedMedias[$md5NodeValue]->getTranslatedContent() : '');

            if(empty($searchVal)) continue;

            $contentAttr->value = str_replace(
                $searchVal,
                $replaceVal,
                $contentAttrValue
            );
        }


        $content = $dom->saveHTML();

        return $content;
    }
}
