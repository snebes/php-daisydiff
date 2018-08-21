<?php

declare(strict_types=1);

namespace DaisyDiff\RangeDifferencer;

use DaisyDiff\RangeDifferencer\Core\LCS;

/**
 * Longest Common Subsequence RangeComparator
 */
class RangeComparatorLCS extends LCS
{
    /** @var RangeComparatorInterface */
    private $comparator1;

    /** @var RangeComparatorInterface */
    private $comparator2;

    /** @var int[][] */
    private $lcs;

    /**
     * @param RangeComparatorInterface $comparator1
     * @param RangeComparatorInterface $comparator2
     */
    public function __construct(RangeComparatorInterface $comparator1, RangeComparatorInterface $comparator2)
    {
        $this->comparator1 = $comparator1;
        $this->comparator2 = $comparator2;
    }

    /**
     * @param  RangeComparatorInterface $left
     * @param  RangeComparatorInterface $right
     * @return RangeDifference[]
     */
    public static function findDifferences(
        RangeComparatorInterface $left,
        RangeComparatorInterface $right
    ): array {
        $lcs = new static($left, $right);
        $lcs->longestCommonSubsequence();

        return $lcs->getDifferences();
    }

    /** {@inheritdoc} */
    public function getLength1(): int
    {
        return $this->comparator1->getRangeCount();
    }

    /** {@inheritdoc} */
    public function getLength2(): int
    {
        return $this->comparator2->getRangeCount();
    }

    /** {@inheritdoc} */
    protected function initializeLcs(int $lcsLength): void
    {
        $this->lcs = array_fill(0, 2, array_fill(0, $lcsLength, 0));
    }

    /** {@inheritdoc} */
    protected function isRangeEqual(int $i1, int $i2): bool
    {
        return $this->comparator1->rangesEqual($i1, $this->comparator2, $i2);
    }

    /** {@inheritdoc} */
    protected function setLcs(int $sl1, int $sl2): void
    {
        // Add one to the values so that 0 can mean that the slot is empty.
        $this->lcs[0][$sl1] = $sl1 + 1;
        $this->lcs[1][$sl1] = $sl2 + 1;
    }

    /**
     * @return RangeDifference[]
     */
    public function getDifferences(): array
    {
        $differences = [];
        $length      = $this->getLength();

        if ($length == 0) {
            $differences[] = RangeDifferenceFactory::createRangeDifference(
                RangeDifference::CHANGE,
                0, $this->comparator2->getRangeCount(),
                0, $this->comparator1->getRangeCount());
        } else {
            $index1 = 0;
            $index2 = 0;
            $s1     = -1;
            $s2     = -1;

            while ($index1 < count($this->lcs[0]) && $index2 < count($this->lcs[1])) {
                // Move both LCS lists to the next occupied slot.
                while (($l1 = $this->lcs[0][$index1]) == 0) {
                    $index1++;

                    if ($index1 >= count($this->lcs[0])) {
                        break;
                    }
                }

                if ($index1 >= count($this->lcs[0])) {
                    break;
                }

                while (($l2 = $this->lcs[1][$index2]) == 0) {
                    $index2++;

                    if ($index2 >= count($this->lcs[1])) {
                        break;
                    }
                }

                if ($index2 >= count($this->lcs[1])) {
                    break;
                }

                // Convert the entry to an array index (see setLcs(int, int)).
                $end1 = $l1 - 1;
                $end2 = $l2 - 1;

                if ($s1 == -1 && ($end1 != 0 || $end2 != 0)) {
                    // There is a diff at the beginning.
                    // TODO: We need to confirm that this is the proper order.
                    $differences[] = RangeDifferenceFactory::createRangeDifference(
                        RangeDifference::CHANGE,
                        0, $end2,
                        0, $end1);
                } elseif ($end1 != $s1 + 1 || $end2 != $s2 + 1) {
                    // A diff was found on one of the sides.
                    $leftStart   = $s1 + 1;
                    $leftLength  = $end1 - $leftStart;
                    $rightStart  = $s2 + 1;
                    $rightLength = $end2 - $rightStart;

                    // TODO: We need to confirm that this is the proper order.
                    $differences[] = RangeDifferenceFactory::createRangeDifference(
                        RangeDifference::CHANGE,
                        $rightStart, $rightLength,
                        $leftStart, $leftLength);
                }

                $s1 = $end1;
                $s2 = $end2;
                $index1++;
                $index2++;
            }

            if ($s1 != -1 &&
                ($s1 + 1 < $this->comparator1->getRangeCount() || $s2 + 1 < $this->comparator2->getRangeCount())) {
                // TODO: we need to find the proper way of representing an append.
                $leftStart  = $s1 < $this->comparator1->getRangeCount() ? $s1 + 1 : $s1;
                $rightStart = $s2 < $this->comparator2->getRangeCount() ? $s2 + 1 : $s2;

                // TODO: We need to confirm that this is the proper order.
                $differences[] = RangeDifferenceFactory::createRangeDifference(
                    RangeDifference::CHANGE,
                    $rightStart, $this->comparator2->getRangeCount() - ($s2 + 1),
                    $leftStart, $this->comparator1->getRangeCount() - ($s1 + 1));
            }
        }

        return $differences;
    }

    /**
     * This method takes an LCS result interspersed with zeros (i.e. empty slots from the LCS algorithm), compacts it
     * and shifts the LCS chunks as far towards the front as possible. This tends to produce good results most of the
     * time.
     *
     * @param  int[]                    $lcsSide
     * @param  int                      $length
     * @param  RangeComparatorInterface $comparator
     * @return void
     */
    private function compactAndShiftLCS(array &$lcsSide, int $length, RangeComparatorInterface $comparator): void
    {
        // If the LCS is empty, just return.
        if (0 == $length) {
            return;
        }

        // Skip any leading empty slots.
        $j = 0;

        while (0 == $lcsSide[$j]) {
            $j++;
        }

        // Put the first non-empty value in position 0.
        $lcsSide[0] = $lcsSide[$j];
        $j++;

        // Push all non-empty values down into the first N slots (where N is the length).
        for ($i = 1; $i < $length; $i++) {
            while (0 == $lcsSide[$j]) {
                $j++;
            }

            // Push the difference down as far as possible by comparing the line at the start of the diff with the line
            // and the end and adjusting if they are the same.
            $nextLine = $lcsSide[$i - 1] + 1;

            if ($nextLine != $lcsSide[$j] &&
                $comparator->rangesEqual($nextLine - 1, $comparator, $lcsSide[$j] - 1)) {
                $lcsSide[$i] = $nextLine;
            } else {
                $lcsSide[$i] = $lcsSide[$j];
            }

            $j++;
        }

        // Zero all slots after the length.
        for ($i = $length; $i < count($lcsSide); $i++) {
            $lcsSide[$i] = 0;
        }
    }

    /**
     * @return void
     */
    public function longestCommonSubsequence(): void
    {
        parent::longestCommonSubsequence();

        if (null !== $this->lcs) {
            // The LCS can be null if one of the sides is empty.
            $this->compactAndShiftLCS($this->lcs[0], $this->getLength(), $this->comparator1);
            $this->compactAndShiftLCS($this->lcs[1], $this->getLength(), $this->comparator2);
        }
    }
}
