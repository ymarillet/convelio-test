<?php

namespace App;

use App\Context\ApplicationContext;
use App\Entity\Quote;
use App\Entity\Template;
use App\Entity\User;
use App\Repository\DestinationRepository;
use App\Repository\QuoteRepository;
use App\Repository\SiteRepository;

class TemplateManager
{
    /** @var ApplicationContext */
    private $applicationContext;

    /** @var QuoteRepository */
    private $quoteRepository;

    /** @var SiteRepository */
    private $siteRepository;

    /** @var DestinationRepository */
    private $destinationRepository;

    public function __construct(
        ApplicationContext $applicationContext,
        QuoteRepository $quoteRepository,
        SiteRepository $siteRepository,
        DestinationRepository $destinationRepository
    ) {
        $this->applicationContext = $applicationContext;
        $this->quoteRepository = $quoteRepository;
        $this->siteRepository = $siteRepository;
        $this->destinationRepository = $destinationRepository;
    }

    /**
     * @return Template
     */
    public function getTemplateComputed(Template $tpl, array $data)
    {
        $replaced = clone($tpl);
        $replaced->subject = $this->computeText($replaced->subject, $data);
        $replaced->content = $this->computeText($replaced->content, $data);

        return $replaced;
    }

    private function computeText($text, array $data)
    {
        $applicationContext = ApplicationContext::getInstance();

        $quote = (isset($data['quote']) and $data['quote'] instanceof Quote) ? $data['quote'] : null;

        if ($quote) {
            $_quoteFromRepository = QuoteRepository::getInstance()->getById($quote->id);
            $usefulObject = SiteRepository::getInstance()->getById($quote->siteId);
            $destinationOfQuote = DestinationRepository::getInstance()->getById($quote->destinationId);

            if (false !== strpos($text, '[quote:destination_link]')) {
                $destination = DestinationRepository::getInstance()->getById($quote->destinationId);
            }

            $containsSummaryHtml = strpos($text, '[quote:summary_html]');
            $containsSummary = strpos($text, '[quote:summary]');

            if ($containsSummaryHtml !== false || $containsSummary !== false) {
                if ($containsSummaryHtml !== false) {
                    $text = str_replace(
                        '[quote:summary_html]',
                        Quote::renderHtml($_quoteFromRepository),
                        $text
                    );
                }
                if ($containsSummary !== false) {
                    $text = str_replace(
                        '[quote:summary]',
                        Quote::renderText($_quoteFromRepository),
                        $text
                    );
                }
            }

            if (false !== strpos($text, '[quote:destination_name]')) {
                $text = str_replace('[quote:destination_name]', $destinationOfQuote->countryName, $text);
            }
        }

        if (isset($destination)) {
            $text = str_replace(
                '[quote:destination_link]',
                $usefulObject->url . '/' . $destination->countryName . '/quote/' . $_quoteFromRepository->id,
                $text
            );
        } else {
            $text = str_replace('[quote:destination_link]', '', $text);
        }

        /*
         * USER
         * [user:*]
         */
        $user = (isset($data['user']) and ($data['user'] instanceof User)) ? $data['user'] : $applicationContext->getCurrentUser();
        if ($user) {
            (strpos($text, '[user:first_name]') !== false) and $text = str_replace('[user:first_name]',
                ucfirst(mb_strtolower($user->firstname)), $text);
        }

        return $text;
    }
}
