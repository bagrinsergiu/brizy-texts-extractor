<?php

namespace BrizyTextsExtractor\Concern;

trait StringUtilsAware
{
    protected function cleanString($string)
    {
        $string1 = $this->trim($string);
        if (strlen($string1) > 0) {
            return $string1;
        }

        return null;
    }

    private function trim($text)
    {
       return trim($text," \t\n\r\0\x0B\xC2\xA0");
    }
}