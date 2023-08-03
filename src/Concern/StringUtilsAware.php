<?php

namespace BrizyTextsExtractor\Concern;

trait StringUtilsAware
{
    protected function cleanString($string)
    {
        $string1 = trim($string);
        if (strlen($string1) > 0)
            return $string1;

        return null;
    }
}