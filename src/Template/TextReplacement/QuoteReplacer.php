<?php

namespace App\Template\TextReplacement;

use App\Entity\Quote;
use App\Repository\DestinationRepository;
use App\Repository\QuoteRepository;
use App\Repository\SiteRepository;

class QuoteReplacer implements Replacer
{
    /** @var QuoteRepository */
    private $quoteRepository;

    /** @var SiteRepository */
    private $siteRepository;

    /** @var DestinationRepository */
    private $destinationRepository;

    /** @var Replacer */
    private $nextReplacer;

    public function __construct(
        QuoteRepository $quoteRepository,
        SiteRepository $siteRepository,
        DestinationRepository $destinationRepository,
        Replacer $nextReplacer
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->siteRepository = $siteRepository;
        $this->destinationRepository = $destinationRepository;
        $this->nextReplacer = $nextReplacer;
    }

    public function replace($text, $context)
    {
        $quote = (isset($context['quote']) and $context['quote'] instanceof Quote) ? $context['quote'] : null;

        if (null === $quote) {
            return $this->nextReplacer->replace($text, $context);
        }
        
        $quoteFromRepository = $this->quoteRepository->getById($quote->id);
        $usefulObject = $this->siteRepository->getById($quote->siteId);
        $destinationOfQuote = $this->destinationRepository->getById($quote->destinationId);

        if (false !== strpos($text, '[quote:summary_html]')) {
            $text = str_replace(
                '[quote:summary_html]',
                Quote::renderHtml($quoteFromRepository),
                $text
            );
        }

        if (false !== strpos($text, '[quote:summary]')) {
            $text = str_replace(
                '[quote:summary]',
                Quote::renderText($quoteFromRepository),
                $text
            );
        }

        if (false !== strpos($text, '[quote:destination_name]')) {
            $text = str_replace(
                '[quote:destination_name]',
                $destinationOfQuote->countryName,
                $text
            );
        }

        if (false !== strpos($text, '[quote:destination_link]')) {
            $destination = DestinationRepository::getInstance()->getById($quote->destinationId);
            $text = str_replace(
                '[quote:destination_link]',
                $usefulObject->url . '/' . $destination->countryName . '/quote/' . $quoteFromRepository->id,
                $text
            );
        } else {
            $text = str_replace('[quote:destination_link]', '', $text);
        }

        return $this->nextReplacer->replace($text, $context);
    }
}
