<?php

namespace DaisyDiff\RangeDifferencer;

/**
 * Create RangeDifference objects.
 */
class RangeDifferenceFactory
{
    /**
     * @param  int $kind
     * @param  int $rightStart
     * @param  int $rightLength
     * @param  int $leftStart
     * @param  int $leftLength
     * @param  int $ancestorStart
     * @param  int $ancestorLength
     * @return RangeDifference
     */
    public static function createRangeDifference(
        int $kind = RangeDifference::NOCHANGE,
        int $rightStart = 0,
        int $rightLength = 0,
        int $leftStart = 0,
        int $leftLength = 0,
        int $ancestorStart = 0,
        int $ancestorLength = 0
    ): RangeDifference {
        $diff = new RangeDifference(
            $kind,
            $rightStart,
            $rightLength,
            $leftStart,
            $leftLength,
            $ancestorStart,
            $ancestorLength);

        return $diff;
    }
}