<?php

namespace App\Template\TextReplacement;

use App\Context\ApplicationContext;
use App\Entity\User;

class UserReplacer implements Replacer
{
    /** @var ApplicationContext */
    private $applicationContext;

    /** @var Replacer */
    private $nextReplacer;

    public function __construct(ApplicationContext $applicationContext, Replacer $nextReplacer)
    {
        $this->applicationContext = $applicationContext;
        $this->nextReplacer = $nextReplacer;
    }

    public function replace($text, $context)
    {
        $user = (isset($data['user']) && ($data['user'] instanceof User)) ? $data['user'] : $this->applicationContext->getCurrentUser();
        $this->replaceFirstName($user, $text);

        return $this->nextReplacer->replace($text, $context);
    }

    private function replaceFirstName(User $user, &$text)
    {
        if (false === strpos($text, '[user:first_name]')) {
            return;
        }

        $text = str_replace('[user:first_name]', ucfirst(mb_strtolower($user->firstname)), $text);
    }
}
