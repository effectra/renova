<?php

declare(strict_types=1);

namespace Effectra\Renova;

/**
 * Class View
 *
 * This class provides methods for rendering views.
 */
class View
{
    protected static array $functions = [];
    protected static array $globalVars = [];

    /**
     * Render a view with the provided data.
     *
     * @param string $path The path to the view file.
     * @param mixed $data The data to be passed to the view.
     * @return string The rendered view content.
     */
    public static function render(string $path, mixed $data = []): string
    {
        $functionsRegistered = self::registered();

        if (!empty(static::$functions)) {
            foreach (static::$functions as $item) {
                $functionsRegistered[] = $item;
            }
        }

        return (new Render($path, $data, $functionsRegistered, static::$globalVars, new Reader()))->send();
    }

    /**
     * Get the registered functions.
     *
     * @param array $functions Additional functions to be registered.
     * @return array The registered functions.
     */
    public static function registered(array $functions = []): array
    {
        return static::$functions;
    }

    /**
     * Register functions or global variables.
     *
     * @param array $contents The contents to be registered.
     * @param string $type The type of contents to register ('functions', 'globalVars', or 'vars').
     * @return void
     * @throws Exception When an undefined type is specified.
     */
    public static function register(array $contents = [], string $type = 'functions'): void
    {
        foreach ($contents as $content) {
            if ($type === 'functions') {
                static::$functions[] = $content;
            }
            if (in_array($type, ['globalVars', 'vars'])) {
                static::$globalVars[] = $content;
            } else {
                throw new \Exception("Undefined type");
            }
        }
    }
}
