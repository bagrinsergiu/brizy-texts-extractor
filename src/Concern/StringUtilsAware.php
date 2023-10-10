<?php

namespace BrizyTextsExtractor\Concern;

trait StringUtilsAware
{
    protected function cleanString($string)
    {
        $string1 = $this->trim($string);
        if (strlen($string1) > 0)
            return $string1;

        return null;
    }

    private function trim($text)
    {
        $trim = trim($text);
        $trim = preg_replace('/^\s*/', "", $trim);
        $trim = preg_replace('/\s*$/', "", $trim);
        return $trim;
    }
}