<?php declare(strict_types=1);

namespace DaisyDiff\Output;

use DaisyDiff\Html\Dom\TagNode;

/**
 * Interface for classes that need to process the result from the tree-like represenation of the output.
 */
interface DiffOutputInterface
{
    /**
     * Parses a Node Tree and produces an output format.
     *
     * @param  TagNode $root
     * @return void
     */
    public function generateOutput(TagNode $node): void;
}
