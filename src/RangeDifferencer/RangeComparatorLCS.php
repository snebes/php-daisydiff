<?php
/**
 * (c) Steve Nebes <snebes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace DaisyDiff\RangeDifferencer;

use DaisyDiff\RangeDifferencer\Core\LCS;
use DaisyDiff\RangeDifferencer\Core\LCSSettings;

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
    private $lcs = [];

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
     * @param RangeComparatorInterface $left
     * @param RangeComparatorInterface $right
     * @param LCSSettings              $settings
     * @return RangeDifference[]
     */
    public static function findDifferences(
        RangeComparatorInterface $left,
        RangeComparatorInterface $right,
        LCSSettings $settings
    ): array {
        $lcs = new static($left, $right);
        $lcs->longestCommonSubsequence($settings);

        return $lcs->getDifferences();
    }

    /**
     * {@inheritdoc}
     */
    public function getLength1(): int
    {
        return $this->comparator1->getRangeCount();
    }

    /**
     * {@inheritdoc}
     */
    public function getLength2(): int
    {
        return $this->comparator2->getRangeCount();
    }

    /**
     * {@inheritdoc}
     */
    protected function initializeLcs(int $lcsLength): void
    {
        $this->lcs = \array_fill(0, 2, \array_fill(0, $lcsLength, 0));
    }

    /**
     * {@inheritdoc}
     */
    protected function isRangeEqual(int $i1, int $i2): bool
    {
        return $this->comparator1->rangesEqual($i1, $this->comparator2, $i2);
    }

    /**
     * {@inheritdoc}
     */
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
        $length = $this->getLength();

        if (0 === $length) {
            $differences[] = new RangeDifference(
                RangeDifference::CHANGE,
                0, $this->comparator2->getRangeCount(),
                0, $this->comparator1->getRangeCount());
        } else {
            $index1 = 0;
            $index2 = 0;
            $s1 = -1;
            $s2 = -1;

            while ($index1 < \count($this->lcs[0]) && $index2 < \count($this->lcs[1])) {
                // Move both LCS lists to the next occupied slot.
                while (0 === ($l1 = $this->lcs[0][$index1])) {
                    $index1++;

                    if ($index1 >= \count($this->lcs[0])) {
                        break;
                    }
                }

                if ($index1 >= \count($this->lcs[0])) {
                    break;
                }

                while (0 === ($l2 = $this->lcs[1][$index2])) {
                    $index2++;

                    if ($index2 >= \count($this->lcs[1])) {
                        break;
                    }
                }

                if ($index2 >= \count($this->lcs[1])) {
                    break;
                }

                // Convert the entry to an array index (see setLcs(int, int)).
                $end1 = $l1 - 1;
                $end2 = $l2 - 1;

                if ($s1 === -1 && ($end1 !== 0 || $end2 !== 0)) {
                    // There is a diff at the beginning.
                    // TODO: We need to confirm that this is the proper order.
                    $differences[] = new RangeDifference(
                        RangeDifference::CHANGE,
                        0, $end2,
                        0, $end1);
                } elseif ($end1 !== $s1 + 1 || $end2 !== $s2 + 1) {
                    // A diff was found on one of the sides.
                    $leftStart = $s1 + 1;
                    $leftLength = $end1 - $leftStart;
                    $rightStart = $s2 + 1;
                    $rightLength = $end2 - $rightStart;

                    // TODO: We need to confirm that this is the proper order.
                    $differences[] = new RangeDifference(
                        RangeDifference::CHANGE,
                        $rightStart, $rightLength,
                        $leftStart, $leftLength);
                }

                $s1 = $end1;
                $s2 = $end2;
                $index1++;
                $index2++;
            }

            if ($s1 !== -1 &&
                ($s1 + 1 < $this->comparator1->getRangeCount() || $s2 + 1 < $this->comparator2->getRangeCount())) {
                // TODO: we need to find the proper way of representing an append.
                $leftStart = $s1 < $this->comparator1->getRangeCount() ? $s1 + 1 : $s1;
                $rightStart = $s2 < $this->comparator2->getRangeCount() ? $s2 + 1 : $s2;

                // TODO: We need to confirm that this is the proper order.
                $differences[] = new RangeDifference(
                    RangeDifference::CHANGE,
                    $rightStart, $this->comparator2->getRangeCount() - ($s2 + 1),
                    $leftStart, $this->comparator1->getRangeCount() - ($s1 + 1));
            }
        }

        return $differences;
    }
}
