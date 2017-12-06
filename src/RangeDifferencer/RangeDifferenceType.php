<?php declare(strict_types=1);

namespace DaisyDiff\RangeDifferencer;

/**
 * RangeDifferenceType model.
 */
final class RangeDifferenceType
{
    /** @const */
    const NOCHANGE  = 0;
    const CHANGE    = 2;
    const CONFLICT  = 1;
    const RIGHT     = 2;
    const LEFT      = 3;
    const ANCESTOR  = 4;
    const ERROR     = 5;

    const INSERT = 0;
    const DELETE = 1;
}
