<?php declare(strict_types=1);

namespace DaisyDiff\RangeDifferencer;

use DaisyDiff\RangeDifferencer\Core\LCSSettings;

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
        if (null === $settings) {
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
        if (null === $ancestor) {
            return self::findDifferences($left, $right, $settings);
        }

        $leftAncestorScript  = null;
        $rightAncestorScript = self::findDifferences($ancestor, $right, $settings);

        if (!empty($rightAncestorScript)) {
            $leftAncestorScript = self::findDifferences($ancestor, $left, $settings);
        }

        if (null === $leftAncestorScript || null === $rightAncestorScript) {
            return [];
        }

        $myIter   = new DifferencesIterator($rightAncestorScript);
        $yourIter = new DifferencesIterator($leftAncestorScript);

        // Prime array with a sentinal.
        $diff3 = [];
        $diff3[] = new RangeDifference(RangeDifference::ERROR);

        // Combine the two-way edit scripts into one.
        while (null !== $myIter->getDifference() || null !== $yourIter->getDifference()) {
            $myIter->removeAll();
            $yourIter->removeAll();

            // Take the next diff that is closer to the start.
            if (null === $myIter->getDifference()) {
                $startThread = $yourIter;
            }
            elseif (null === $yourIter->getDifference()) {
                $startThread = $myIter;
            }
            else {
                // Not at end of both scripts take the lowest range.
                if ($myIter->getDifference()->leftStart() <= $yourIter->getDifference()->leftStart()) {
                    $startThread = $myIter;
                } else {
                    $startThread = $yourIter;
                }
            }

            $changeRangeStart = $startThread->getDifference()->leftStart();
            $changeRangeEnd   = $startThread->getDifference()->leftEnd();
            $startThread->next();

            // Check for overlapping changes with other thread merge overlapping changes with this range.
            $other = $startThread->other($myIter, $yourIter);

            while (null !== $other->getDifference() && $other->getDifference()->leftStart() <= $changeRangeEnd) {
                $newMax = $other->getDifference()->leftEnd();
                $other->next();

                if ($newMax >= $changeRangeEnd) {
                    $changeRangeEnd = $newMax;
                    $other = $other->other($myIter, $yourIter);
                }
            }

            $diff3[] = self::createRangeDifference3(
                $myIter, $yourIter, $diff3,
                $right, $left,
                $changeRangeStart, $changeRangeEnd);
        }

        // Remove sentinal, the first item in the array.
        array_shift($diff3);

        return array_values($diff3);
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
        if (null === $ancestor) {
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
            $mstart, $right->getRangeCount() - $mstart,
            $ystart, $left->getRangeCount() - $ystart,
            $astart, $ancestor->getRangeCount() - $astart);

        if ($rd->maxLength() > 0) {
            $out[] = $rd;
        }

        return $out;
    }

    /**
     * @param  DifferencesIterator      $myIter
     * @param  DifferencesIterator      $yourIter
     * @param  array                    $diff3
     * @param  RangeComparatorInterface $right
     * @param  RangeComparatorInterface $left
     * @param  int                      $changeRangeStart
     * @param  int                      $changeRangeEnd
     * @return RangeDifference
     */
    private static function createRangeDifference3(
        DifferencesIterator $myIter,
        DifferencesIterator $yourIter,
        array &$diff3,
        RangeComparatorInterface $right,
        RangeComparatorInterface $left,
        int $changeRangeStart = 0,
        int $changeRangeEnd = 0
    ): RangeDifference {
        $kind = RangeDifference::ERROR;

        /** @var RangeDifference $last */
        $last = $diff3[count($diff3) - 1];

        // At least one range array must be non-empty.
        assert(0 != $myIter->getCount() || 0 != $yourIter->getCount());

        // Find corresponding lines to fChangeRangeStart/End in right and left.
        if (0 == $myIter->getCount()) {
            // Only left changed.
            $rightStart = $changeRangeStart - $last->ancestorEnd() + $last->rightEnd();
            $rightEnd   = $changeRangeEnd - $last->ancestorEnd() + $last->rightEnd();
            $kind = RangeDifference::LEFT;
        } else {
            /** @var RangeDifference[] $range */
            $range = $myIter->getRange();
            $f = $range[0];
            $l = $range[count($range) - 1];
            $rightStart = $changeRangeStart - $f->leftStart() + $f->rightStart();
            $rightEnd   = $changeRangeEnd - $l->leftEnd() + $l->rightEnd();
        }

        if (0 == $yourIter->getCount()) {
            // Only right changed.
            $leftStart = $changeRangeStart - $last->ancestorEnd() + $last->leftEnd();
            $leftEnd   = $changeRangeEnd - $last->ancestorEnd() + $last->leftEnd();
            $kind = RangeDifference::RIGHT;
        } else {
            /** @var RangeDifference[] $range */
            $range = $yourIter->getRange();
            $f = $range[0];
            $l = $range[count($range) - 1];
            $leftStart = $changeRangeStart - $f->leftStart() + $f->rightStart();
            $leftEnd   = $changeRangeEnd - $l->leftEnd() + $l->rightEnd();
        }

        if (RangeDifference::ERROR == $kind) {
            // Overlapping change (conflict).
            if (self::rangeSpansEqual(
                $right, $rightStart, $rightEnd - $rightStart, $left, $leftStart, $leftEnd - $leftStart)) {
                $kind = RangeDifference::ANCESTOR;
            } else {
                $kind = RangeDifference::CONFLICT;
            }
        }

        return new RangeDifference(
            $kind,
            $rightStart, $rightEnd - $rightStart,
            $leftStart, $leftEnd - $leftStart,
            $changeRangeStart, $changeRangeEnd - $changeRangeStart);
    }

    /**
     * @param  RangeComparatorInterface $right
     * @param  int                      $rightStart
     * @param  int                      $rightLen
     * @param  RangeComparatorInterface $left
     * @param  int                      $leftStart
     * @param  int                      $leftLen
     * @return bool
     */
    private static function rangeSpansEqual(
        RangeComparatorInterface $right,
        int $rightStart,
        int $rightLen,
        RangeComparatorInterface $left,
        int $leftStart,
        int $leftLen
    ): bool {
        if ($rightLen == $leftLen) {
            for ($i = 0; $i < $rightLen; $i++) {
                if (!self::rangesEqual($right, $rightStart + $i, $left, $leftStart + $i)) {
                    break;
                }
            }

            if ($i == $rightLen) {
                return true;
            }
        }

        return false;
    }

    /**
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
}
