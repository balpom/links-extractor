<?php

declare(strict_types=1);

namespace Balpom\LinksExtractor\Extractors;

use Balpom\LinksExtractor\SanitizerInterface;
use Balpom\LinksExtractor\LinksExtractorException;

class HtmlSanitizer implements SanitizerInterface
{

    public function __construct()
    {
        if (false === function_exists('tidy_repair_string')) {
            throw new LinksExtractorException('PHP Tidy extension required, but not installed. You can write your own HtmlSanitizer or your own ExtractorFactory with your own LinksExtractor from HTML.');
        }
    }

    public function sanitize(string $string): string
    {
        $opts = ['output-xhtml' => true, 'wrap' => 0, 'numeric-entities' => true];
        $html = tidy_repair_string($string, $opts);

        return $html;
    }
}
