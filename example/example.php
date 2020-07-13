<?php

namespace Example;

use App\Context\ApplicationContext;
use App\Entity\Quote;
use App\Entity\Template;
use App\Repository\DestinationRepository;
use App\Repository\QuoteRepository;
use App\Repository\SiteRepository;
use App\Template\TextReplacement\NothingReplacer;
use App\Template\TextReplacement\QuoteReplacer;
use App\Template\TextReplacement\UserReplacer;
use App\TemplateManager;

require_once __DIR__ . '/../vendor/autoload.php';

$faker = \Faker\Factory::create();

$template = new Template(
    1,
    'Votre livraison à [quote:destination_name]',
    "
Bonjour [user:first_name],

Merci de nous avoir contacté pour votre livraison à [quote:destination_name].

Bien cordialement,

L'équipe Convelio.com
");
$templateManager = new TemplateManager(
    new UserReplacer(
        ApplicationContext::getInstance(),
        new QuoteReplacer(
            QuoteRepository::getInstance(),
            SiteRepository::getInstance(),
            DestinationRepository::getInstance(),
            new NothingReplacer()
        )
    )
);

$message = $templateManager->getTemplateComputed(
    $template,
    [
        'quote' => new Quote($faker->randomNumber(), $faker->randomNumber(), $faker->randomNumber(), $faker->date()),
    ]
);

echo $message->subject . "\n" . $message->content;
