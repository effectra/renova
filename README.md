# Effectra\Renova

Effectra\Renova is a PHP package that provides a template rendering and web asset management functionality. It includes a template engine for rendering template files and a web asset management system for generating script and link tags for web assets.

## Installation

You can install the Effectra\Renova package via Composer. Simply run the following command:

```
composer require effectra/renova
```

## Usage

### Template Engine

The template engine allows you to render template files using a template syntax. Here's an example of how to use the template engine:

```php
use Effectra\Renova\TemplateEngine;

$template = new TemplateEngine();

// Render a template file
$content = (new Render(
    $path,
    $data,
    [
        ['url' => function ($path = '') {
                return Request::url() . (string) $path;
            }]
    ],
    [
        ['APP_NAME' => $_ENV['APP_NAME'] ]
    ],
    $this->reader
))->send();

$links = $this->encore->linkTags('app');
$scripts = $this->encore->scriptTags('app');

$content = $this->addLinksAndScripts($content, $links, $scripts);
```

In the example above, we create a new instance of the `TemplateEngine` class indirectly by instantiating the `Render` class. The `Render` class uses the `TemplateEngine` internally for rendering the template file. We pass the necessary arguments to the `Render` constructor, including the template file path, data, template functions, template global variables, and an instance of the `Reader` class.

### Web Asset Management

The web asset management feature allows you to generate script and link tags for web assets managed by Encore. Here's an example of how to use the web asset management:

```php
use Effectra\Renova\WebEncore;

$webEncore = new WebEncore();

// Generate script tags for a specific section
$scriptTags = $webEncore->scriptTags('app');

// Generate link tags for a specific section
$linkTags = $webEncore->linkTags('styles');
```

In the example above, we create a new instance of the `WebEncore` class. We then use the `scriptTags` and `linkTags` methods to generate the script and link tags for the specified sections.

## Contributing

Thank you for considering contributing to the Effectra\Renova package! If you would like to contribute, please follow the guidelines in the [CONTRIBUTING.md](link-to-contributing-md) file.

## License

The Effectra\Renova package is open-source software licensed under the [MIT license](link-to-license-file).
