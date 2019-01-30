<?php
/**
 * (c) Steve Nebes <snebes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

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
     * @param TextNodeComparator $leftComparator
     * @param TextNodeComparator $rightComparator
     * @return void
     * @throws Exception
     */
    public function diff(TextNodeComparator $leftComparator, TextNodeComparator $rightComparator): void;
}
