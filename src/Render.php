<?php

declare(strict_types=1);

namespace Effectra\Renova;

/**
 * Class Render
 *
 * This class handles rendering of template files using a template engine.
 */
class Render
{
    protected string $content;
    protected mixed $data;
    protected array $functionRegistered;
    protected array $globalVarsRegistered;

    /**
     * Render constructor.
     *
     * @param string $path The path to the template file.
     * @param mixed $data The data to be passed to the template.
     * @param array $functionRegistered The registered functions for the template engine.
     * @param array $globalVarsRegistered The registered global variables for the template engine.
     * @param Reader $reader The reader instance to handle template file reading.
     */
    public function __construct(
        string $path,
        mixed $data,
        array $functionRegistered,
        array $globalVarsRegistered,
        protected Reader $reader
    ) {
        $this->content = $reader->file($path, $data);
        $this->data = $reader->validData($data);
        $this->functionRegistered = $functionRegistered;
        $this->globalVarsRegistered = $globalVarsRegistered;
    }

    /**
     * Render and return the template content.
     *
     * @return string The rendered template content.
     */
    public function send(): string
    {
        $templateEngine = new TemplateEngine();

        foreach ($this->functionRegistered as $fn) {
            $templateEngine->registerFunction(key($fn), $fn[key($fn)]);
        }

        foreach ($this->globalVarsRegistered as $fn) {
            $templateEngine->registerGlobalVars(key($fn), $fn[key($fn)]);
        }

        $html = $templateEngine->render($this->content, $this->data);

        return $html;
    }

    /**
     * Convert the rendered template content to a string.
     *
     * @return string The rendered template content.
     */
    public function __toString(): string
    {
        return $this->send();
    }
}
