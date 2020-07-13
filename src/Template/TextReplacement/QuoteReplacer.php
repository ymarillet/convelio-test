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

        // this is questionable ... why would we need to get another Quote from a repository since we already got one
        // in the context ? Maybe they should not be the same class objects. @see with the team
        $quoteFromRepository = $this->quoteRepository->getById($quote->id);

        $this->replaceSummary($text, $quoteFromRepository);
        $this->replaceDestination($text, $quote, $quoteFromRepository);

        return $this->nextReplacer->replace($text, $context);
    }

    private function replaceDestination(&$text, Quote $quote, Quote $quoteFromRepository)
    {
        $destination = $this->destinationRepository->getById($quote->destinationId);

        if (false !== strpos($text, '[quote:destination_name]')) {
            $text = str_replace(
                '[quote:destination_name]',
                $destination->countryName,
                $text
            );
        }

        if (false !== strpos($text, '[quote:destination_link]')) {
            $site = $this->siteRepository->getById($quote->siteId);

            $text = str_replace(
                '[quote:destination_link]',
                $site->url . '/' . $destination->countryName . '/quote/' . $quoteFromRepository->id,
                $text
            );
        }
    }

    /**
     * @param string $text
     * @param Quote $quote
     *
     * @return void
     */
    private function replaceSummary(&$text, Quote $quote)
    {
        if (false !== strpos($text, '[quote:summary_html]')) {
            $text = str_replace(
                '[quote:summary_html]',
                Quote::renderHtml($quote),
                $text
            );
        }

        if (false !== strpos($text, '[quote:summary]')) {
            $text = str_replace(
                '[quote:summary]',
                Quote::renderText($quote),
                $text
            );
        }
    }
}
