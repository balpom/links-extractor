# links-extractor
## Extract all links from string and provide it in extended PSR-13 format.

This extractor extract all HTTP links from given string and provide it in Balpom\WebLink\EvolvableWebLinkInterface [balpom/web-link](https://github.com/balpom/web-link) format (which extends Psr\Link\EvolvableLinkInterface ([https://www.php-fig.org/psr/psr-13/](https://www.php-fig.org/psr/psr-13/)) format) as collection in Psr\Link\LinkProviderInterface format (see also [https://www.php-fig.org/psr/psr-13/](https://www.php-fig.org/psr/psr-13/)).

### Requirements 
- **PHP >= 8.1**

### Installation
#### Using composer (recommended)
```bash
composer require balpom/links-extractor
```

### How to use
There are two extractor realisation in this package:
Balpom\LinksExtractor\SimpleExtractor and Balpom\LinksExtractor\Extractor

**SimpleExtractor** usage samples:
```php
$css = 'div.cls1{background-image:url(/image-file-1.jpg);}span.cls2{background-image:url(/image-file-2.png);}';

$extractor = new SimpleExtractor($css, 'text/css');
$linksProvider = $extractor->extract();
print_r($linksProvider->getLinks());
```

```php
$html = '<a href="/page.html">ABC</a><a href="/page.html">XYZ</a>
<img src="/image-file-3.gif"><div>Else one <img src="/image-file-3.gif"></div>';

$extractor = new SimpleExtractor($html, 'text/html');
$linksProvider = $extractor->extract();
print_r($linksProvider->getLinks());
```

**Extractor** works also as a SimpleExtractor, but takes as a constructor argument an Psr\Http\Message\ResponseInterface object ([https://www.php-fig.org/psr/psr-7/](https://www.php-fig.org/psr/psr-7/)), not a string.

### License
MIT License See [LICENSE.MD](LICENSE.MD)