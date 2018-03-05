<?php

namespace DaisyDiff\Output;

use Exception;

/**
 * Interface for classes that need to process the result from the tag-like representation of the output.
 */
interface TextDiffOutputInterface
{
    /**
     * @param  string $text
     * @throws Exception
     */
    public function addClearPart(string $text): void;

    /**
     * @param  string $text
     * @throws Exception
     */
    public function addRemovedPart(string $text): void;

    /**
     * @param  string $text
     * @throws Exception
     */
    public function addAddedPart(string $text): void;
}
