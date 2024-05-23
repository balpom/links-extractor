<?php

declare(strict_types=1);

namespace Balpom\LinksExtractor;

final class DefaultSanitizer implements SanitizerInterface
{

    public function sanitize(string $string): string
    {
        return $string;
    }
}
