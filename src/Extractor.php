<?php 

declare(strict_types=1);

namespace Balpom\LinksExtractor;

use Psr\Http\Message\ResponseInterface;
use Psr\Link\EvolvableLinkProviderInterface;
use Symfony\Component\WebLink\GenericLinkProvider;

class Extractor extends AbstractExtractor
{

    private ResponseInterface|null $response;

    public function __construct(ResponseInterface|null $response = null,
            ExtractorFactoryInterface|null $factory = null)
    {
        $this->response = $response;
        if (null === $factory) {
            $factory = new ExtractorFactory();
        }
        $this->factory = $factory;
    }

    public function extract(): EvolvableLinkProviderInterface
    {
        if (null === $this->response ||
                false === ($mime = $this->getMimeType($this->response)) ||
                false === ($extractor = $this->factory->getExtractor($mime))
        ) {
            return new GenericLinkProvider(); // Empty links collection.
        }

        try {
            $content = $this->response->getBody()->getContents();
        } catch (\Exception $e) {
            return new GenericLinkProvider(); // Empty links collection.
        }

        if (empty($content)) {
            return new GenericLinkProvider(); // Empty links collection.
        }

        return $extractor->extractFromString($content);
    }

    protected function getMimeType(ResponseInterface $response)
    {
        $mime = strtolower($response->getHeaderLine('Content-Type'));
        $mime = explode(';', $mime, 2);
        $mime = trim($mime[0]);

        return empty($mime) ? false : $mime;
    }
}
