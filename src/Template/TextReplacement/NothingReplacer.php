<?php

namespace App\Template\TextReplacement;

class NothingReplacer implements Replacer
{
    public function replace($text, $context)
    {
        return $text;
    }
}
