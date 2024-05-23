<?php

declare(strict_types=1);

namespace Balpom\LinksExtractor\Extractors;

use Psr\Link\EvolvableLinkProviderInterface;
use Symfony\Component\WebLink\GenericLinkProvider;
use Balpom\LinksExtractor\LinksExtractorException;
use Sabberworm\CSS\Parser;
use Sabberworm\CSS\Value\URL;

class Css extends Common
{

    public function extractFromString(string $string, array|null $params = null): EvolvableLinkProviderInterface
    {
        $links = new GenericLinkProvider();
        $urls = $this->getUrls($string);
        $tag = null;
        if (is_array($params) && isset($params['tag']) && !empty($params['tag'])) {
            $tag = $params['tag'];
        }

        foreach ($urls as $url) {
            $link = $this->createLinkFromString($url, $tag);
            $links = $links->withLink($link);
        }

        return $links;
    }

    protected function getUrls(string $string): array
    {
        try {
            $parser = new Parser($string);
        } catch (Exception $e) {
            throw new LinksExtractorException('Unable to create CSS parser.');
        }

        $urls = [];
        $cssDocument = $parser->parse();

        $elements = ['background-image', 'src'];
        foreach ($elements as $element) {
            $objects = $cssDocument->getAllValues($element);
            foreach ($objects as $object) {
                if (!($object instanceof URL)) {
                    continue;
                }
                $url = $object->getURL()->getString();
                $url = trim($url);
                $substr = strtolower(substr($url, 0, 5));
                if ('data:' === $substr) {
                    continue;
                }
                if (!in_array($url, $urls)) {
                    $urls[] = $url;
                }
            }
        }

        return $urls;
    }
}
