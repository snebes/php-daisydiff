<?php declare(strict_types=1);

namespace DaisyDiff\RangeDifferencer;

/**
 * For breaking an object to compare into a sequence of comparable entities.
 *
 * It is used by <code>RangeDifferencer</code> to find longest sequences of matching and non-matching ranges.
 *
 * For example, to compare two text documents and find longest common sequences of matching and non-matching lines, the
 * implementation must break the document into lines. getRangeCount would return the number of lines in the document,
 * and rangesEqual would compare a specified line given with one in another RangeComparatorInterface.
 *
 * Clients should implement this interface; there is no standard implementation.
 */
interface RangeComparatorInterface
{
    /**
     * Returns the number of comparable entities.
     *
     * @return int
     */
    public function getRangeCount(): int;

    /**
     * Returns whether the comparable entity given by the first index matches an entity specified by the other
     * RangeComparatorInterface and index.
     *
     * @param  int                      $thisIndex
     * @param  RangeComparatorInterface $other
     * @param  int                      $otherIndex
     * @return bool
     */
    public function rangesEqual(): bool;

    /**
     * Returns whether a comparison should be skipped because it would be too costly (or lengthy).
     *
     * @param  int                      $length
     * @param  int                      $maxLength
     * @param  RangeComparatorInterface $other
     * @return bool
     */
    public function skipRangeComparison(int $length, int $maxLength, RangeComparatorInterface $other): bool;
}
