<?php

declare(strict_types=1);

namespace Balpom\LinksExtractor;

use Psr\Link\EvolvableLinkProviderInterface;
use Symfony\Component\WebLink\GenericLinkProvider;

class SimpleExtractor extends AbstractExtractor
{

    private string|null $string;
    private string|null $mime;
    private ExtractorFactoryInterface|null $factory;

    public function __construct(string|null $string = null,
            string|null $mime = 'text/html',
            ExtractorFactoryInterface|null $factory = null)
    {
        $this->string = $string;
        $this->mime = $mime;
        if (null === $factory) {
            $factory = new ExtractorFactory();
        }
        $this->factory = $factory;
    }

    public function extract(): EvolvableLinkProviderInterface
    {
        if (empty($this->string) ||
                empty($this->mime) ||
                false === ($extractor = $this->factory->getExtractor($this->mime))
        ) {
            return new GenericLinkProvider(); // Empty links collection.
        }

        return $extractor->extractFromString($this->string);
    }
}
