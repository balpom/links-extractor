<?php

declare(strict_types=1);

namespace Balpom\LinksExtractor\Extractors;

use Balpom\LinksExtractor\SanitizerInterface;
use Balpom\LinksExtractor\DefaultSanitizer;
use Psr\Link\EvolvableLinkProviderInterface;
use Symfony\Component\WebLink\GenericLinkProvider;
use \DOMDocument;
use \DOMXpath;

class Html extends Common
{

    private SanitizerInterface $sanitizer; // Repair HTML code, if it has errors.
    private SanitizerInterface $modifier; // Modify HTML code (as sample, cut needless tags).

    public function __construct(
            SanitizerInterface|null $sanitizer = null,
            SanitizerInterface|null $modifier = null
    )
    {
        $this->sanitizer = (null === $sanitizer) ? new DefaultSanitizer() : $sanitizer;
        $this->modifier = (null === $modifier) ? new DefaultSanitizer() : $modifier;
    }

    public function extractFromString(string $string, array|null $params = null): EvolvableLinkProviderInterface
    {
        // Use SanitizerInterface.
        $html = $this->sanitizer->sanitize($string);
        $html = $this->modifier->sanitize($html);

        $doc = new DOMDocument();
        @$doc->loadXML($html); // Otherwise "Namespace prefix xlink for href on use is not defined in Entity".
        $xpath = new DOMXpath($doc);
        $xpath->registerNamespace('xhtml', 'http://www.w3.org/1999/xhtml');
        $links = $this->extractLinks($xpath);

        return $links;
    }

    protected function extractLinks(DOMXpath $xpath): EvolvableLinkProviderInterface
    {
        $links = new GenericLinkProvider();

        $conditions = [
            'contains(@property, ":image") and not(contains(@property, ":image:"))',
            'contains(@property, ":url")',
            'starts-with(@name, "msapplication-") and contains(@name, "TileImage")',
            'starts-with(@name, "msapplication-") and contains(@name, "logo")'
        ];
        foreach ($conditions as $condition) {
            $nodes = $xpath->query('//xhtml:meta[' . $condition . ']');
            foreach ($nodes as $node) {
                if (null !== ($link = $this->createLinkFromNode($node, 'content'))) {
                    $links = $links->withLink($link);
                }
            }
        }

        $tags = ['a', 'link', 'img', 'script', 'iframe', 'source'];
        foreach ($tags as $tag) {
            $nodes = $xpath->query('//xhtml:' . $tag);
            foreach ($nodes as $node) {
                if (null !== ($link = $this->createLinkFromNode($node))) {
                    $links = $links->withLink($link);
                }
            }
        }

        $extractor = new Css(); // From CSS link extractor.

        $nodes = $xpath->query('//xhtml:style'); // <style> tag.
        foreach ($nodes as $node) {
            $css = $node->nodeValue;
            $cssLinks = $extractor->extractFromString($css, ['tag' => 'style']);
            foreach ($cssLinks as $link) {
                $links = $links->withLink($link);
            }
        }

        $nodes = $xpath->query('//xhtml:*[@style]'); // <tag style="..." ...>...</tag>
        foreach ($nodes as $node) {
            $css = $node->getAttribute('style');
            $css = $this->sanitizeCss($css);
            $tag = $node->nodeName;
            $css = $tag . '{' . $css . '}'; // Otherwise not valid CSS.
            $cssLinks = $extractor->extractFromString($css, ['tag' => $tag]);
            foreach ($cssLinks as $link) {
                $links = $links->withLink($link);
            }
        }

        return $links;
    }

    protected function sanitizeCss(string $css)
    {
        $css = str_replace('&#x27', "'", $css);
        //$css = str_replace("'", "", $css);

        return $css;
    }
}
