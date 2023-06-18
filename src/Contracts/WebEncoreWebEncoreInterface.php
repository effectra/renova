<?php

declare(strict_types=1);

namespace Effectra\Renova\Contracts;

use Exception;

/**
 * Interface WebEncoreInterface
 *
 * This interface defines methods for generating Encore entry script and link tags.
 */
interface WebEncoreInterface
{
    /**
     * Generate the Encore entry script tags for the specified section.
     *
     * @param string $section The section name.
     * @return string|null The generated script tags, or null if an error occurs.
     * @throws Exception If an error occurs during processing.
     */
    public function scriptTags(string $section): ?string;

    /**
     * Generate the Encore entry link tags for the specified section.
     *
     * @param string $section The section name.
     * @return string|null The generated link tags, or null if an error occurs.
     * @throws Exception If an error occurs during processing.
     */
    public function linkTags(string $section): ?string;
}
