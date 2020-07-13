<?php

namespace App;

use App\Entity\Template;
use App\Template\TextReplacement\Replacer;

class TemplateManager
{
    /** @var Replacer */
    private $replacer;

    public function __construct(Replacer $replacer)
    {
        $this->replacer = $replacer;
    }

    /**
     * @return Template
     */
    public function getTemplateComputed(Template $tpl, array $data)
    {
        $replaced = clone($tpl);
        $replaced->subject = $this->replacer->replace($replaced->subject, $data);
        $replaced->content = $this->replacer->replace($replaced->content, $data);

        return $replaced;
    }
}
