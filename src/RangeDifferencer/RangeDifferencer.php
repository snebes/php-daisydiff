<?php declare(strict_types=1);

namespace DaisyDiff\RangeDifferencer;

use DaisyDiff\RangeDifferencer\Core\LCSSettings;
use RuntimeException;

/**
 * A RangeDifferencer finds the differences between two or three RangeComparatorInterfaces.
 *
 * To use the differencer, clients provide an RangeComparatorInterface that breaks their input data into a sequence of
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
     * @param  RangeComparatorInterface $left
     * @param  RangeComparatorInterface $right
     * @param  LCSSettings              $settings
     * @return RangeDifference[]
     */
    public static function findDifferences(
        RangeComparatorInterface $left,
        RangeComparatorInterface $right,
        ?LCSSettings $settings = null
    ): array {
        if (null == $settings) {
            $settings = new LCSSettings();
        }

        if (!$settings->isUseGreedyMethod()) {
            return OldDifferencer::findDifferences($left, $right);
        }

        return RangeComparatorLCS::findDifferences($left, $right, $settings);
    }

    /**
     * Finds the differences among three RangeComparatorInterfaces. The differences are returned as a list of
     * RangeDifferences. If no differences are detected an empty list is returned. If the ancestor range comparator is
     * null, a two-way comparison is performed.
     *
     * @param  RangeComparatorInterface $ancestor
     * @param  RangeComparatorInterface $left
     * @param  RangeComparatorInterface $right
     * @param  LCSSettings              $settings
     * @return RangeDifference[]
     */
    public static function findDifferences3(
        ?RangeComparatorInterface $ancestor,
        RangeComparatorInterface $left,
        RangeComparatorInterface $right,
        ?LCSSettings $settings = null
    ): array {
        if (null == $ancestor) {
            return self::findDifferences($left, $right);
        }
//
//        $leftAncestorScript  = [];
//        $rightAncestorScript = self::findDifferences($ancestor, $right, $settings);
//
//        if (!empty($rightAncestorScript)) {
//            $leftAncestorScript = self::findDifferences($ancestor, $left, $settings);
//        }
//
//        if (empty($leftAncestorScript) || empty($rightAncestorScript)) {
//            return [];
//        }
    }

    /**
     * @param  RangeComparatorInterface $left
     * @param  RangeComparatorInterface $right
     * @param  LCSSettings|null         $settings
     * @return RangeDifference[]
     */
    public static function findRanges(
        RangeComparatorInterface $left,
        RangeComparatorInterface $right,
        ?LCSSettings $settings = null
    ): array {
        $in  = self::findDifferences($left, $right, $settings);
        $out = [];

        $mstart = 0;
        $ystart = 0;

        for ($i = 0, $iMax = count($in); $i < $iMax; $i++) {
            $es = $in[$i];
            $rd = new RangeDifference(RangeDifference::NOCHANGE,
                $mstart, $es->rightStart() - $mstart,
                $ystart, $es->leftStart() - $ystart);

            if ($rd->maxLength() != 0) {
                $out[] = $rd;
            }

            $out[] = $es;

            $mstart = $es->rightEnd();
            $ystart = $es->leftEnd();
        }

        $rd = new RangeDifference(RangeDifference::NOCHANGE,
            $mstart, $right->getRangeCount() - $mstart,
            $ystart, $left->getRangeCount() - $ystart);

        if ($rd->maxLength() > 0) {
            $out[] = $rd;
        }

        return $out;
    }

    /**
     * @param  RangeComparatorInterface|null $ancestor
     * @param  RangeComparatorInterface      $left
     * @param  RangeComparatorInterface      $right
     * @param  LCSSettings|null              $settings
     * @return array
     */
    public static function findRanges3(
        ?RangeComparatorInterface $ancestor,
        RangeComparatorInterface $left,
        RangeComparatorInterface $right,
        ?LCSSettings $settings = null
    ): array {
        if (null == $ancestor) {
            return self::findRanges($left, $right, $settings);
        }

        $in  = self::findDifferences3($ancestor, $left, $right, $settings);
        $out = [];

        $mstart = 0;
        $ystart = 0;
        $astart = 0;

        for ($i = 0, $iMax = count($in); $i < $iMax; $i++) {
            $es = $in[$i];
            $rd = new RangeDifference(RangeDifference::NOCHANGE,
                $mstart, $es->rightStart() - $mstart,
                $ystart, $es->leftStart() - $ystart,
                $astart, $es->ancestorStart() - $astart);

            if ($rd->maxLength() > 0) {
                $out[] = $rd;
            }

            $out[] = $es;

            $mstart = $es->rightEnd();
            $ystart = $es->leftEnd();
            $astart = $es->ancestorEnd();
        }

        $rd = new RangeDifference(RangeDifference::NOCHANGE,
            $mstart, $es->getRangeCount() - $mstart,
            $ystart, $es->getRangeCount() - $ystart);

        if ($rd->maxLength() > 0) {
            $out[] = $rd;
        }

        return $out;
    }
}
