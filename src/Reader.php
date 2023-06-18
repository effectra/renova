<?php

declare(strict_types=1);

namespace Effectra\Renova;

use Effectra\Fs\File;
use Effectra\Fs\Path;
use Exception;

/**
 * Class Reader
 * 
 * This class provides functionality for reading template files, extracting data, and performing validations.
 */
class Reader
{
    /**
     * Read a template file and render it with the provided data.
     *
     * @param string $path The path to the template file.
     * @param array $data The data to be passed to the template.
     * @return string The rendered template content.
     * @throws Exception if the view file does not exist.
     */
    public function file(string $path, array $data): string
    {
        $file = Path::removeExtension($path) . '.php';

        if (!File::exists($file)) {
            throw new Exception("View file '{$file}' does not exist!");
        }

        ob_start();
        extract($data);
        include_once $file;
        return ob_get_clean();
    }

    /**
     * Get a value from the provided data array using the specified key.
     *
     * @param string $key The key to retrieve the value for.
     * @param array $data The data array.
     * @return mixed|null The value associated with the key or null if it doesn't exist.
     */
    public function getFromData(string $key, array $data)
    {
        return $data[$key] ?? null;
    }

    /**
     * Get all the keys from the provided data array.
     *
     * @param array $data The data array.
     * @return array The keys from the data array.
     */
    public function getKeys(array $data): array
    {
        return array_keys($data);
    }

    /**
     * Extract data variables from the provided template content.
     *
     * @param string $content The template content.
     * @return array The extracted data variables.
     */
    public function getDataFromFile(string $content): array
    {
        $entries = $this->getAll($content);
        return $entries;
    }

    /**
     * Get all data variables enclosed within double curly braces from the provided entry.
     *
     * @param string $entry The entry content.
     * @return array The extracted data variables.
     */
    public static function getAll(string $entry): array
    {
        preg_match_all("/(?<=\{\{)(.*?)(?=\}\})/", $entry, $matches);

        foreach ($matches[1] as $match) {
            $reads[] = trim($match);
        }
        return $reads;
    }

    /**
     * Validate and sanitize the provided data array.
     *
     * @param array $data The data array to be validated.
     * @return array The validated and sanitized data array.
     * @throws Exception if an invalid data key is encountered.
     */
    public function validData(array $data): array
    {
        $dataValidate = [];
        foreach ($data as $key => $value) {
            if (!is_string($key)) {
                throw new Exception("Invalid data 'key' must be of type string!");
            }

            if (is_int($value) || is_float($value)) {
                $dataValidate[$key] = "$value";
            } else {
                $dataValidate[$key] = $value;
            }
        }
        return $dataValidate;
    }
}
