<?php

declare(strict_types=1);

namespace Balpom\LinksExtractor;

use Psr\Link\EvolvableLinkProviderInterface;

interface LinksExtractorInterface
{

    public function extract(): EvolvableLinkProviderInterface;
}
