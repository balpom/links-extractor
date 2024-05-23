<?php

declare(strict_types=1);

namespace Balpom\LinksExtractor\Extractors;

use Psr\Link\EvolvableLinkProviderInterface;
use Balpom\WebLink\WebLink;
use \DOMNamedNodeMap;
use \DOMNode;

abstract class Common
{

    abstract public function extractFromString(string $string, array|null $params = null): EvolvableLinkProviderInterface;

    protected function createLinkFromString(string $href, string|null $tag = null): WebLink|null
    {
        if (empty($href)) {
            return null;
        }
        $href = $this->sanitizeHref($href);
        $link = new WebLink(null, $href);
        if (!empty($tag)) {
            $link = $link->withTag($tag);
        }

        return $link;
    }

    protected function createLinkFromNode(DOMNode $node, array|string|null $linkAttributes = null): WebLink|null
    {
        if (empty($linkAttributes)) {
            $href = $this->getHrefFromNode($node);
        } else {
            if (is_string($linkAttributes)) {
                $linkAttributes = [$linkAttributes];
            }
            $href = $this->getHrefFromNode($node, $linkAttributes);
        }
        if (null === $href) {
            return null;
        }

        $rel = $this->getRelFromNode($node);
        $attributes = $this->getAttributes($node->attributes);
        $tag = $node->nodeName;  // For DOMElement tagName
        $content = $this->getInnerHTML($node);
        $href = $this->sanitizeHref($href);

        $link = new WebLink($rel, $href);
        $link = $link->withTag($tag)->withContent($content);
        foreach ($attributes as $attribute => $value) {
            $link = $link->withAttribute($attribute, $value);
        }

        return $link;
    }

    protected function getHrefFromNode(DOMNode $node, array $attributes = ['href', 'src', 'srcset']): string|null
    {
        foreach ($attributes as $attribute) {
            if ($node->hasAttribute($attribute)) {
                return $node->getAttribute($attribute);
            }
        }

        return null;
    }

    protected function getRelFromNode(DOMNode $node): string|null
    {
        if ($node->hasAttribute('rel')) {
            return $node->getAttribute('rel');
        }

        return null;
    }

    protected function getAttributes(DOMNamedNodeMap $attributes): array
    {
        $result = [];
        foreach ($attributes as $attribute) { // DOMAttr
            $result[$attribute->name] = $attribute->value;
        }

        return $result;
    }

    protected function getInnerHTML(DOMNode $node): string
    {
        $innerHTML = '';
        $children = $node->childNodes;
        foreach ($children as $child) {
            $innerHTML .= $child->ownerDocument->saveXML($child);
        }

        return trim($innerHTML);
    }

    protected function sanitizeHref(string $href)
    {
        if (false !== strpos($href, ' ')) {
            $href = explode(' ', $href);
            $href = $href[0];
        }

        return trim($href);
    }
}
