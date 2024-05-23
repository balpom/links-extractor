<?php

declare(strict_types=1);

namespace Balpom\LinksExtractor;

use Balpom\LinksExtractor\Extractors\Common;

interface ExtractorFactoryInterface
{

    public function getExtractor(string $mime): Common|false;
}
