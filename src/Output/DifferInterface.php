<?php

namespace DaisyDiff\Output;

use DaisyDiff\Html\TextNodeComparator;
use Exception;

/**
 * Interface for classes that are interested in the tree-like result structure as produced by DaisyDiff.
 */
interface DifferInterface
{
    /**
     * Compares two Node Trees.
     *
     * @param  TextNodeComparator $leftComparator
     * @param  TextNodeComparator $rightComparator
     * @return void
     * @throws Exception
     */
    public function diff(TextNodeComparator $leftComparator, TextNodeComparator $rightComparator): void;
}
