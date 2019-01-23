<?php
/**
 * (c) Steve Nebes <snebes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace DaisyDiff\RangeDifferencer;

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
     * @param RangeComparatorInterface $left
     * @param RangeComparatorInterface $right
     * @return RangeDifference[]
     */
    public static function findDifferences(RangeComparatorInterface $left, RangeComparatorInterface $right): array
    {
        // Assert that both RangeComparatorInterface are of the same class.
        assert(get_class($right) === get_class($left));

        $rightSize = $right->getRangeCount();
        $leftSize = $left->getRangeCount();

        // Differences matrix:
        // Only the last d of each diagonal is stored, i.e., $lastDiagonal[$k] = row of d
        $diagLen = (int) (2 * \max($rightSize, $leftSize));
        $maxDiagonal = $diagLen;

        /** @var int[] The row containing the last d */
        $lastDiagonal = \array_fill(0, $diagLen + 1, 0);

        // On diagonal $k ($lastDiagonal[$k] = $row
        $origin = (int) ($diagLen / 2);

        // Script corresponding to $d[$k]
        /** @var LinkedRangeDifference */
        $script = \array_fill(0, $diagLen + 1, null);
        $row = 0;

        // Find common prefix.
        while ($row < $rightSize && $row < $leftSize && self::rangesEqual($right, $row, $left, $row)) {
            $row++;
        }

        $lastDiagonal[$origin] = $row;
        $script[$origin] = null;

        $lower = ($row === $rightSize) ? $origin + 1 : $origin - 1;
        $upper = ($row === $leftSize) ? $origin - 1 : $origin + 1;

        if ($lower > $upper) {
            return [];
        }

        // For each value of the edit distance.
        for ($d = 1; $d <= $maxDiagonal; ++$d) {
            // $d is the current edit distance.

            if ($right->skipRangeComparison($d, $maxDiagonal, $left)) {
                // This condition always returns false, so the following code is never executed.
                // It should be something we already found.
                return []; // @codeCoverageIgnore
            }

            // For each relevant diagonal (-d, -d+2, ... d-2, d)
            for ($k = $lower; $k <= $upper; $k += 2) {
                // $k is the current diagonal.
                if ($k === $origin - $d || $k !== $origin + $d && $lastDiagonal[$k + 1] >= $lastDiagonal[$k - 1]) {
                    // Move down.
                    $row = $lastDiagonal[$k + 1] + 1;
                    $edit = new LinkedRangeDifference($script[$k + 1], LinkedRangeDifference::DELETE);
                } else {
                    // Move right.
                    $row = $lastDiagonal[$k - 1];
                    $edit = new LinkedRangeDifference($script[$k - 1], LinkedRangeDifference::INSERT);
                }

                $col = $row + $k - $origin;

                $edit->setRightStart($row);
                $edit->setLeftStart($col);

                assert($k >= 0 && $k <= $maxDiagonal);
                $script[$k] = $edit;

                // Slide down the diagonal as far as possible.
                while ($row < $rightSize && $col < $leftSize && self::rangesEqual($right, $row, $left, $col)) {
                    ++$row;
                    ++$col;
                }

                // Unreasonable value for diagonal index.
                assert($k >= 0 && $k <= $maxDiagonal);
                $lastDiagonal[$k] = $row;

                if ($row === $rightSize && $col === $leftSize) {
                    return self::createDifferencesRanges($script[$k]);
                }

                if ($row === $rightSize) {
                    $lower = $k + 2;
                }

                if ($col === $leftSize) {
                    $upper = $k - 2;
                }
            }

            --$lower;
            ++$upper;
        }

        // Too many differences.
        throw new \RuntimeException(); // @codeCoverageIgnore
    }

    /**
     * Tests if two ranges are equal
     *
     * @param RangeComparatorInterface $a
     * @param int                      $ai
     * @param RangeComparatorInterface $b
     * @param int                      $bi
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
     * @param LinkedRangeDifference $start
     * @return RangeDifference[]
     */
    private static function createDifferencesRanges(LinkedRangeDifference $start): array
    {
        $ep = self::reverseDifferences($start);
        $result = [];

        while (null !== $ep) {
            $es = new RangeDifference(RangeDifference::CHANGE);

            if ($ep->isInsert()) {
                $es->setRightStart($ep->getRightStart() + 1);
                $es->setLeftStart($ep->getLeftStart() + 1);
                $b = $ep;

                do {
                    $ep = $ep->getNext();
                    $es->setLeftLength($es->getLeftLength() + 1);
                } while (null !== $ep && $ep->isInsert() && $ep->getRightStart() === $b->getRightStart());
            } else {
                $es->setRightStart($ep->getRightStart());
                $es->setLeftStart($ep->getLeftStart());

                // Deleted lines.
                do {
                    $a = $ep;
                    $ep = $ep->getNext();
                    $es->setRightLength($es->getRightLength() + 1);
                } while (null !== $ep && $ep->isDelete() && $ep->getRightStart() === $a->getRightStart() + 1);

                $change = (null !== $ep && $ep->isInsert() && $ep->getRightStart() === $a->getRightStart());

                if ($change) {
                    $b = $ep;

                    // Replacement lines.
                    do {
                        $ep = $ep->getNext();
                        $es->setLeftLength($es->getLeftLength() + 1);
                    } while (null !== $ep && $ep->isInsert() && $ep->getRightStart() === $b->getRightStart());
                } else {
                    $es->setLeftLength(0);
                }

                // Meaning of range changes from "insert after", to "replace with".
                $es->setLeftStart($es->getLeftStart() + 1);
            }

            // The script commands are 1 based, subtract one to make them zero based.
            $es->setRightStart($es->getRightStart() - 1);
            $es->setLeftStart($es->getLeftStart() - 1);

            $result[] = $es;
        }

        return $result;
    }

    /**
     * Reverses the range differences.
     *
     * @param LinkedRangeDifference $start
     * @return LinkedRangeDifference
     */
    private static function reverseDifferences(LinkedRangeDifference $start): LinkedRangeDifference
    {
        $ahead = $start;
        $ep = null;

        while (null !== $ahead) {
            $behind = $ep;
            $ep = $ahead;
            $ahead = $ahead->getNext();
            $ep->setNext($behind);
        }

        return $ep;
    }
}
