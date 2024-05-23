<?php

declare(strict_types=1);

namespace Balpom\LinksExtractor;

interface SanitizerInterface
{

    public function sanitize(string $string): string;
}
