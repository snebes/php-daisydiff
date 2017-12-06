<?php declare(strict_types=1);

namespace DaisyDiff\RangeDifferencer;

use RuntimeException;

/**
 * The algorithm used is an objectified version of one described in: A File Comparison Program, by Webb Miller and
 * Eugene W. Myers, Software Practice and Experience, Vol. 15, Nov. 1985.
 */
final class OldDifferencer
{
    /**
     * Prevent class instantiation.
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
        if (!$settings->useGreedyMethod()) {
            return OldDifferencer::findDifferences($left, $right);
        }

        throw RuntimeException('This is not implemented.');
    }
}
