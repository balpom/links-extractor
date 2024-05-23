<?php

declare(strict_types=1);

namespace Balpom\LinksExtractor;

use Psr\Link\EvolvableLinkProviderInterface;

abstract class AbstractExtractor implements LinksExtractorInterface
{

    abstract public function extract(): EvolvableLinkProviderInterface;
}
