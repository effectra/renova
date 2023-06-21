<?php

declare(strict_types=1);

namespace Effectra\Renova;

/**
 * Class TemplateEngine
 *
 * This class handles the rendering of template files using a template engine.
 */
class TemplateEngine
{
    protected array $globalVars = [];
    protected array $functions = [];

    /**
     * Register global variables for the template engine.
     *
     * @param string $name The name of the global variable.
     * @param mixed $callable The value or callable to associate with the global variable.
     */
    public function registerGlobalVars(string $name, $callable): void
    {
        $this->globalVars[$name] = $callable;
    }

    /**
     * Register functions for the template engine.
     *
     * @param string $name The name of the function.
     * @param callable $callable The callable function to register.
     */
    public function registerFunction(string $name, callable $callable): void
    {
        $this->functions[$name] = $callable;
    }

    /**
     * Render the template content using the provided data.
     *
     * @param string $content The template content to render.
     * @param array $data The data to be passed to the template.
     * @return string The rendered template content.
     */
    public function render(string $content, array $data = []): string
    {
        $content = $this->renderVar($content, $data);
        $content = $this->renderGlobalVars($content);
        $content = $this->renderFunctions($content, $data);
        $content = $this->renderLoops($content, $data);
        $content = $this->renderIfCondition($content);

        $content = str_replace(['{{', '}}'], '', $content);

        return $content;
    }

    /**
     * Render loops in the template content.
     *
     * @param string $content The template content.
     * @param array $data The data to be used in the template.
     * @return string The template content with replaced loop placeholders.
     */
    protected function renderLoops(string $content, array $data): string
    {
        preg_match_all('/@foreach\((.*?)\)(.*?)@endforeach/s', $content, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $loopData = $this->getDataFromVariable($match[1], $data);
            $loopContent = '';

            foreach ($loopData[1] as $value) {
                $loopContent .= $this->replaceVariables($match[2], $value, $loopData[0]);
            }

            $content = str_replace($match[0], $loopContent, $content);
        }

        return $content;
    }

    /**
     * Render if conditions in the template content.
     *
     * @param string $content The template content.
     * @return string The template content with replaced if condition placeholders.
     */
    protected function renderIfCondition(string $content): string
    {
        $content = preg_replace_callback('/@if(.+?)@endif/s', function ($matches) {
            $condition = trim($matches[1]);
            $condition = str_replace('$', '', $condition);
            return "<?php if ($condition): ?>";
        }, $content);

        return $content;
    }

    /**
     * Get data from a variable in the provided data array.
     *
     * @param string $variable The variable name.
     * @param array $data The data array.
     * @return array The variable name and its corresponding data array.
     */
    protected function getDataFromVariable(string $variable, array $data): array
    {
        $var = trim($variable, '$');
        $var_data = $data[$var] ?? [];

        return [$var, $var_data];
    }

    /**
     * Replace variables in the content with their corresponding values from the data array.
     *
     * @param string $content The content to replace variables in.
     * @param array $data The data array.
     * @param string $keyData The key data string.
     * @return string The content with replaced variables.
     */
    protected function replaceVariables(string $content, array $data, string $keyData = ''): string
    {
        foreach ($data as $key => $value) {
            $content = str_replace('$' . $keyData . '->' . $key, $value, $content);
        }
        return $content;
    }

    /**
     * Render variables in the template content.
     *
     * @param string $content The template content.
     * @param array $data The data to be used in the template.
     * @return string The template content with replaced variable placeholders.
     */
    public function renderVar(string $content, array $data): string
    {
        foreach ($data as $key => $value) {
            if (!is_array($value)) {
                $content = str_replace('$' . $key, $value, $content);
            }
        }
        return $content;
    }

    /**
     * Render function placeholders in the template content.
     *
     * @param string $content The template content.
     * @param array $data The data to be used in the template.
     * @return string The template content with replaced function placeholders.
     */
    protected function renderFunctions(string $content, array $data): string
    {
        $content = preg_replace_callback('/{{\s*([a-zA-Z_][a-zA-Z0-9_]*\s*\(\s*(.*?)\s*\))\s*}}/', function ($matches) use ($data) {
            $function = $matches[1];
            $args = $matches[2];

            $args = $this->parseFunctionArguments($args, $data);

            return $this->executeFunction($function, $args);
        }, $content);

        return $content;
    }

    /**
     * Parses function arguments and converts them to their corresponding values.
     *
     * @param string $args The function arguments.
     * @param array $data The data to be used in the template.
     * @return array The parsed function arguments.
     */
    protected function parseFunctionArguments(string $args, array $data): array
    {
        $args = preg_split('/(?<!\\\),/', $args);
        $args = array_map('trim', $args);

        $parsedArgs = [];
        foreach ($args as $arg) {
            if (preg_match('/^["\'](.*)["\']$/', $arg, $matches)) {
                $parsedArgs[] = $matches[1];
            } elseif (array_key_exists($arg, $data)) {
                $parsedArgs[] = $data[$arg];
            } else {
                $parsedArgs[] = $arg;
            }
        }

        return $parsedArgs;
    }

    /**
     * Executes the function based on its name and arguments.
     *
     * @param string $function The function name and arguments.
     * @param array $args The parsed function arguments.
     * @return string The result of the executed function.
     */
    protected function executeFunction(string $function, array $args): string
    {
        $functionParts = explode('(', $function, 2);
        $functionName = trim($functionParts[0]);
        $functionArgs = trim($functionParts[1], '()');

        $callable = $this->functions[$functionName] ?? null;
        if ($callable && is_callable($callable)) {
            return call_user_func_array($callable, $args);
        } else {
            return '';
        }
    }

    /**
     * Render global variables in the template content.
     *
     * @param string $content The template content.
     * @return string The template content with replaced global variable placeholders.
     */
    public function renderGlobalVars(string $content): string
    {
        foreach ($this->globalVars as $name => $callable) {
            if (is_callable($callable)) {
                $callable = call_user_func($callable, []);
            }
            $content = preg_replace_callback('/{{(' . $name . '+)}}/', function ($matches) use ($callable) {
                return $callable;
            }, $content);
        }
        return $content;
    }

    /**
     * Extract global variables from the template content.
     *
     * @param string $content The template content.
     * @return string|null The extracted global variable name, or null if no global variable is found.
     */
    public function extractGlobalVars(string $content): ?string
    {
        $pattern = '/{{([A-Z]+)}}/';
        preg_match($pattern, $content, $matches);

        if (isset($matches[1])) {
            return $matches[1];
        } else {
            return null;
        }
    }
}
