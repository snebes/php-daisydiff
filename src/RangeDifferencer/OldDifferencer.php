<?php declare(strict_types=1);

namespace DaisyDiff\RangeDifferencer;

use ReflectionProperty;
use RuntimeException;
use SplFixedArray;

/**
 * The algorithm used is an objectified version of one described in: A File Comparison Program, by Webb Miller and
 * Eugene W. Myers, Software Practice and Experience, Vol. 15, Nov. 1985.
 */
final class OldDifferencer
{
    /**
     * Prevent class instantiation.
     *
     * @codeCoverageIgnore
     */
    private function __construct()
    {
    }

    /**
     * Finds the differences between two RangeComparatorInterfaces. The differences are returned as an array of
     * RangeDifferences. If no differences are detected an empty array is returned.
     *
     * @param  RangeComparatorInterface $left
     * @param  RangeComparatorInterface $right
     * @return RangeDifferences[]
     */
    public static function findDifferences(RangeComparatorInterface $left, RangeComparatorInterface $right): iterable
    {
        // Assert that both RangeComparatorInterface are of the same class.
        assert(get_class($right) == get_class($left));

        $rightSize = $right->getRangeCount();
        $leftSize  = $left->getRangeCount();

        // Differences matrix:
        // Only the last d of each diagonal is stored, i.e., $lastDiagonal[$k] = row of d
        $diagLen = 2 * max($rightSize, $leftSize);
        $maxDiagonal = $diagLen;

        /** @var int[] The row containing the last d */
        $lastDiagonal = new SplFixedArray($diagLen + 1);

        // On diagonal $k ($lastDiagonal[$k] = $row
        $origin = intval(round($diagLen / 2, 0));

        // Script corresponding to $d[$k]
        /** @var LinkedRangeDifference */
        $script = new SplFixedArray($diagLen + 1);
        $row = $col = 0;

        // Find common prefix.
        for (; $row < $rightSize && $row < $leftSize && self::rangesEqual($right, $row, $left, $row);) {
            $row++;
        }

        $lastDiagonal[$origin] = $row;
        $script[$origin] = null;

        $lower = ($row == $rightSize)? $origin + 1 : $origin - 1;
        $upper = ($row == $leftSize)?  $origin - 1 : $origin + 1;

        if ($lower > $upper) {
            return [];
        }

        // For each value of the edit distance.
        for ($d = 1; $d <= $maxDiagonal; $d++) {
            // $d is the current edit distance.

            if ($right->skipRangeComparison($d, $maxDiagonal, $left)) {
                // This condition always returns false, so the following code is never executed.
                // It should be something we already found.
                return []; // @codeCoverageIgnore
            }

            // For each relevant diagonal (-d, -d+2, ... d-2, d)
            for ($k = $lower; $k <= $upper; $k += 2) {
                // $k is the current diagonal.
                unset($edit);

                if ($k == $origin - $d || $k != $origin + $d && $lastDiagonal[$k + 1] >= $lastDiagonal[$k - 1]) {
                    // Move down.
                    $row  = $lastDiagonal[$k + 1] + 1;
                    $edit = new LinkedRangeDifference($script[$k + 1], LinkedRangeDifference::DELETE);
                } else {
                    // Move right.
                    $row  = $lastDiagonal[$k - 1];
                    $edit = new LinkedRangeDifference($script[$k - 1], LinkedRangeDifference::INSERT);
                }

                $col = $row + $k - $origin;
                $edit->fRightStart = $row;
                $edit->fLeftStart  = $col;

                assert($k >= 0 && $k <= $maxDiagonal);
                $script[$k] = $edit;

                // Slide down the diagonal as far as possible.
                while ($row < $rightSize && $col < $leftSize && self::rangesEqual($right, $row, $left, $col)) {
                    $row++;
                    $col++;
                }

                assert($k >= 0 && $k <= $maxDiagonal);
                $lastDiagonal[$k] = $row;

                if ($row == $rightSize && $col == $leftSize) {
                    return self::createDifferencesRanges($script[$k]);
                }

                if ($row == $rightSize) {
                    $lower = $k + 2;
                }

                if ($col == $leftSize) {
                    $upper = $k - 2;
                }
            }

            $lower--;
            $upper++;
        }

        // Too many differences.
        throw new RuntimeException('Too many differences to compute.'); // @codeCoverageIgnore
    }

    /**
     * Tests if two ranges are equal
     *
     * @param  RangeComparatorInterface $a
     * @param  int                      $ai
     * @param  RangeComparatorInterface $b
     * @param  int                      $bi
     * @return bool
     */
    private static function rangesEqual(
        RangeComparatorInterface $a,
        int $ai,
        RangeComparatorInterface $b,
        int $bi
    ): bool {
        return $a->rangesEqual($ai, $b, $bi);
    }

    /**
     * Creates a Vector of DifferencesRanges out of the LinkedRangeDifference. It coalesces adjacent changes. In
     * addition, indices are changed such that the ranges are 1) open, i.e, the end of the range is not included, and 2)
     * are zero based.
     *
     * @param  LinkedRangeDifference $start
     * @return RangeDifference[]
     */
    private static function createDifferencesRanges(LinkedRangeDifference $start): iterable
    {
        $ep = self::reverseDifferences($start);
        $result = [];

        while (!is_null($ep)) {
            $es = new RangeDifference(RangeDifferenceType::CHANGE);

            if ($ep->isInsert()) {
                $es->fRightStart = $ep->fRightStart + 1;
                $es->fLeftStart = $ep->fLeftStart;
                $b = $ep;

                do {
                    $ep = $ep->getNext();
                    $es->fLeftLength++;
                } while (!is_null($ep) && $ep->isInsert() && $ep->fRightStart == $b->fRightStart);
            } else {
                $es->fRightStart = $ep->fRightStart;
                $es->fLeftStart  = $ep->fLeftStart;
                $a = $ep;

                // Deleted lines.
                do {
                    $a  = $ep;
                    $ep = $ep->getNext();
                    $es->fRightLength++;
                } while (!is_null($ep) && $ep->isDelete() && $ep->fRightStart == $a->fRightStart + 1);

                $change = (!is_null($ep) && $ep->isInsert() && $ep->fRightStart == $a->fRightStart);

                if ($change) {
                    $b = $ep;

                    // Replacement lines.
                    do {
                        $ep = $ep->getNext();
                        $es->fLeftLength++;
                    } while (!is_null($ep) && $ep->isInsert() && $ep->fRightStart == $b->fRightStart);
                } else {
                    $es->fLeftLength = 0;
                }

                $es->fLeftStart++;
            }

            $es->fRightStart--;
            $es->fLeftStart--;

            $result[] = $es;
        }

        return $result;
    }

    /**
     * Reverses the range differences.
     *
     * @param  LinkedRangeDifference $start
     * @return LinkedRangeDifference
     */
    private static function reverseDifferences(LinkedRangeDifference $start): LinkedRangeDifference
    {
        $ahead = $start;
        $ep = null;

        while (!is_null($ahead)) {
//            unset($behind);
            $behind = $ep;

//            unset($ep);
            $ep = $ahead;

//            unset($ahead);
//            $ahead = $ep->getNext();
            $ahead = $ahead->getNext();
            $ep->setNext($behind);
        }

        return $ep;
    }
}
