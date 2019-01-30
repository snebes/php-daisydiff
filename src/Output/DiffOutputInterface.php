<?php
/**
 * (c) Steve Nebes <snebes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace SN\DaisyDiff\Output;

use SN\DaisyDiff\Html\Dom\TagNode;
use Exception;

/**
 * Interface for classes that need to process the result from the tree-like represenation of the output.
 */
interface DiffOutputInterface
{
    /**
     * Parses a Node Tree and produces an output format.
     *
     * @param TagNode $node
     * @return void
     * @throws Exception
     */
    public function generateOutput(TagNode $node): void;
}
