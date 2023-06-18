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

// Register global variables
$template->registerGlobalVars('siteName', 'My Website');
$template->registerGlobalVars('pageTitle', 'Home');

// Register functions
$template->registerFunction('url', function ($route) {
    // Generate URL based on the provided route
    // Replace this with your actual URL generation logic
    return 'https://example.com/' . $route;
});

// Render a template file
$content = file_get_contents('path/to/template.tpl');
$data = [
    'username' => 'JohnDoe',
    'isLoggedIn' => true,
];
$renderedContent = $template->render($content, $data);
```

In the example above, we create a new instance of the `TemplateEngine` class. We then register global variables and functions using the `registerGlobalVars` and `registerFunction` methods, respectively. Finally, we render a template file by passing the template content and data to the `render` method.

### Web Asset Management

The web asset management feature allows you to generate script and link tags for web assets managed by Encore. Here's an example of how to use the web asset management:

```php
use Effectra\WebEncore;

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