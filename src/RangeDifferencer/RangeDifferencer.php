<?php declare(strict_types=1);

namespace DaisyDiff\RangeDifferencer;

use RuntimeException;

/**
 * A RangeDifferencer finds the differences between two or three RangeComparatorInterfaces.
 *
 * To use the differencer, clients provide an RangeComparatorInterfaces that breaks their input data into a sequence of
 * comparable entities. The differencer returns the differences among these sequences as an array of RangeDifference
 * objects (findDifferences methods). Every RangeDifference represents a single kind of difference and the corresponding
 * ranges of the underlying comparable entities in the left, right, and optionally ancestor sides.
 *
 * Alternatively, the findRanges methods not only return objects for the differing ranges but for non-differing ranges
 * too.
 *
 * The algorithm used is an objectified version of one described in: A File Comparison Program, by Webb Miller and
 * Eugene W. Myers, Software Practice and Experience, Vol. 15, Nov. 1985.
 */
final class RangeDifferencer
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
     * @param  LCSSettings              $settings
     * @param  RangeComparatorInterface $left
     * @param  RangeComparatorInterface $right
     * @return RangeDifferences[]
     */
    public static function findDifferences(
        ?LCSSettings $settings,
        RangeComparatorInterface $left,
        RangeComparatorInterface $right
    ): iterable {
        if (is_null($settings)) {
            $settings = new LCSSettings();
        }

        if (!$settings->useGreedyMethod()) {
            return OldDifferencer::findDifferences($left, $right);
        }

        throw RuntimeException('This is not implemented.');
    }
}
