<?php

namespace App\Template\TextReplacement;

interface Replacer
{
    /**
     * @param string $text
     * @param mixed $context
     *
     * @return string
     */
    public function replace($text, $context);
}
