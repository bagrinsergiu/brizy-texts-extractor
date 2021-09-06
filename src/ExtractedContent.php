<?php

namespace BrizyTextsExtractor;

class ExtractedContent
{
    const TYPE_UNKNOWN = 0;
    const TYPE_TEXT = 1;
    const TYPE_IMAGE = 2;
    const TYPE_IMAGE_ALT = 3;

    /**
     * @var string
     */
    private $content;

    /**
     * @var int
     */
    private $type;

    private $translatedContent;

    public function __toString()
    {
        return $this->content;
    }

    public static function instance($content, $type)
    {
        $instance = new ExtractedContent();
        $instance->setContent($content);
        $instance->setType($type);

        return $instance;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param string $content
     *
     * @return ExtractedContent
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param int $type
     *
     * @return ExtractedContent
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTranslatedContent()
    {
        return $this->translatedContent;
    }

    /**
     * @param mixed $translatedContent
     *
     * @return ExtractedContent
     */
    public function setTranslatedContent($translatedContent): ExtractedContent
    {
        $this->translatedContent = $translatedContent;

        return $this;
    }
}