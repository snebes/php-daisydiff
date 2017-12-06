<?php declare(strict_types=1);

namespace DaisyDiff\Tag;

use DaisyDiff\RangeDifferencer\RangeComparatorInterface;

/**
 * Extens the IRangeComparator interface with functionality to recreate parts of the original document.
 */
interface AtomSplitterInterface extends RangeComparatorInterface
{
    /**
     * @param  int $i
     * @return AtomInterface
     */
    public function getAtom(int $i): AtomInterface;

    /**
     * @param  int $startAtom
     * @param  int $endAtom
     * @return string
     */
    public function substring(int $startAtom, int $endAtom = -1): string;
}