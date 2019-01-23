<?php
/**
 * (c) Steve Nebes <snebes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace DaisyDiff\Tag;

use DaisyDiff\RangeDifferencer\RangeComparatorInterface;

/**
 * Extends RangeComparatorInterface with functionality to recreate parts of the original document.
 */
interface AtomSplitterInterface extends RangeComparatorInterface
{
    /**
     * @param int $i
     * @return AtomInterface
     */
    public function getAtom(int $i): AtomInterface;

    /**
     * @param int      $startAtom
     * @param int|null $endAtom
     * @return string
     */
    public function substring(int $startAtom, int $endAtom = null): string;
}
