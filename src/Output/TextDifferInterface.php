<?php
/**
 * (c) Steve Nebes <snebes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace DaisyDiff\Output;

use DaisyDiff\Tag\AtomSplitterInterface;
use Exception;

/**
 * Interface for classes that are interested in the tag-like result structure as produced by DaisyDiff.
 */
interface TextDifferInterface
{
    /**
     * Compares two Node Trees.
     *
     * @param AtomSplitterInterface $leftComparator
     * @param AtomSplitterInterface $rightComparator
     * @throws Exception
     */
    public function diff(AtomSplitterInterface $leftComparator, AtomSplitterInterface $rightComparator): void;
}
