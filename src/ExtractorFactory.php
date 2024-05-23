<?php

declare(strict_types=1);

namespace Balpom\LinksExtractor;

use Balpom\LinksExtractor\Extractors\Common;

class ExtractorFactory implements ExtractorFactoryInterface
{

    public function getExtractor(string $mime): Common|false
    {
        if (false === ($class = $this->className($mime))) {
            return false;
        }
        $nameSpace = $this->nameSpace();
        $fullClass = $this->fullClassName($nameSpace, $class);

        // Hard coding.
        // You can write your own ExtractorFactory or Extractor (or extend existings).
        if ('Html' === $class) {
            return new($fullClass)(
                    new ($nameSpace . '\HtmlSanitizer'),  null
            );
        }

        return new($fullClass);
    }

    private function fullClassName(string $nameSpace, string $class): string
    {
        if ('\\' <> substr($class, 0, 1)) {
            $class = '\\' . $class;
        }

        return $nameSpace . $class;
    }

    private function className(string $mime): string|false
    {
        $mime = strtolower($mime);
        $classes = $this->classNames();

        return isset($classes[$mime]) ? $classes[$mime] : false;
    }

    private function classNames(): array
    {
        $class = [];
        $class['text/html'] = 'Html';
        $class['text/css'] = 'Css';

        return $class;
    }

    private function nameSpace(): string
    {
        $subdir = 'Extractors';

        return __NAMESPACE__ . '\\' . $subdir;
    }
}
