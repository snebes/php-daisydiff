<?php declare(strict_types=1);

namespace DaisyDiff\RangeDifferencer;

/**
 * Description of a change between two or three ranges of comparable entities.
 *
 * RangeDifference objects are the elements of a compare result returned from the RangeDifferencer find* methods.
 * Clients use these objects as they are returned from the differencer. This class is not intended to be instantiated
 * outside of the Compare framework.
 *
 * Note: A range in the RangeDifference object is given as a start index and length in terms of comparable entities.
 * However, these entity indices and counts are not necessarily character positions. For example, if an entity
 * represents a line in a document, the start index would be a line number and the count would be in lines.
 */
class RangeDifference
{
    /** Two-way change constant indicating no change. */
    const NOCHANGE = 0;

    /** Two-way change constant indicating two-way change (same as RIGHT) */
    const CHANGE = 2;

    /** Three-way change constant indicating a change in both right and left. */
    const CONFLICT = 1;

    /** Three-way change constant indicating a change in right. */
    const RIGHT = 2;

    /** Three-way change constant indicating a change in left. */
    const LEFT = 3;

    /**
     * Three-way change constant indicating the same change in both right and left, that is only the ancestor is
     * different.
     */
    const ANCESTOR = 4;

    /** Constant indicating an unknown change kind. */
    const ERROR = 5;

    /** @var int */
    private $kind = 0;

    /** @var int */
    private $leftStart = 0;

    /** @var int */
    private $leftLength = 0;

    /** @var int */
    private $rightStart = 0;

    /** @var int */
    private $rightLength = 0;

    /** @var int */
    private $ancestorStart = 0;

    /** @var int */
    private $ancestorLength = 0;

    /**
     * @param int $kind
     * @param int $rightStart
     * @param int $rightLength
     * @param int $leftStart
     * @param int $leftLength
     * @param int $ancestorStart
     * @param int $ancestorLength
     */
    public function __construct(
        int $kind,
        int $rightStart = 0,
        int $rightLength = 0,
        int $leftStart = 0,
        int $leftLength = 0,
        int $ancestorStart = 0,
        int $ancestorLength = 0
    ) {
        $this->kind           = $kind;
        $this->rightStart     = $rightStart;
        $this->rightLength    = $rightLength;
        $this->leftStart      = $leftStart;
        $this->leftLength     = $leftLength;
        $this->ancestorStart  = $ancestorStart;
        $this->ancestorLength = $ancestorLength;
    }

    /**
     * Returns the kind of difference.
     *
     * @return int
     */
    public function kind(): int
    {
        return $this->kind;
    }

    /**
     * Returns the start index of the entity range on the ancestor side.
     *
     * @return int
     */
    public function ancestorStart(): int
    {
        return $this->ancestorStart;
    }

    /**
     * Returns the number of entities on the ancestor side.
     *
     * @return int
     */
    public function ancestorLength(): int
    {
        return $this->ancestorLength;
    }

    /**
     * Returns the end index of the entity range on the ancestor side.
     *
     * @return int
     */
    public function ancestorEnd(): int
    {
        return $this->ancestorStart + $this->ancestorLength;
    }

    /**
     * Returns the start index of the entity range on the right side.
     *
     * @return int
     */
    public function rightStart(): int
    {
        return $this->rightStart;
    }

    /**
     * Returns the number of entities on the right side.
     *
     * @return int
     */
    public function rightLength(): int
    {
        return $this->rightLength;
    }

    /**
     * Returns the end index of the entity range on the right side.
     *
     * @return int
     */
    public function rightEnd(): int
    {
        return $this->rightStart + $this->rightLength;
    }

    /**
     * Returns the start index of the entity range on the left side.
     *
     * @return int
     */
    public function leftStart(): int
    {
        return $this->leftStart;
    }

    /**
     * Returns the number of entities on the left side.
     *
     * @return int
     */
    public function leftLength(): int
    {
        return $this->leftLength;
    }

    /**
     * Returns the end index of the entity range on the left side.
     *
     * @return int
     */
    public function leftEnd(): int
    {
        return $this->leftStart + $this->leftLength;
    }

    /**
     * Returns the maximum number of entities in the left, right, and ancestor sides of this range.
     *
     * @return int
     */
    public function maxLength(): int
    {
        return max($this->rightLength, $this->leftLength, $this->ancestorLength);
    }

    /**
     * @param  RangeDifference $other
     * @return bool
     */
    public function equals(RangeDifference $other): bool
    {
        return
            $this->kind == $other->kind() &&
            $this->leftStart == $other->leftStart() &&
            $this->leftLength == $other->leftLength() &&
            $this->rightStart == $other->rightStart() &&
            $this->rightLength == $other->rightLength() &&
            $this->ancestorStart == $other->ancestorStart() &&
            $this->ancestorLength == $other->ancestorLength()
        ;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        $str = 'RangeDifference {';

        switch ($this->kind) {
            case self::NOCHANGE:
                $str .= 'NOCHANGE';
                break;
            case self::CHANGE:
                $str .= 'CHANGE/RIGHT';
                break;
            case self::CONFLICT:
                $str .= 'CONFLICT';
                break;
            case self::LEFT:
                $str .= 'LEFT';
                break;
            case self::ERROR:
                $str .= 'ERROR';
                break;
            case self::ANCESTOR:
                $str .= 'ANCESTOR';
                break;
            default:
                break;
        }

        $str .= ', ';
        $str .= sprintf('Left: %s Right: %s',
            $this->toRangeString($this->leftStart, $this->leftLength),
            $this->toRangeString($this->rightStart, $this->rightLength)
        );

        if ($this->ancestorLength > 0 || $this->ancestorStart > 0) {
            $str .= sprintf(' Ancestor: %s', $this->toRangeString($this->ancestorStart, $this->ancestorLength));
        }

        $str .= '}';

        return $str;
    }

    /**
     * @param  int $start
     * @param  int $length
     * @return string
     */
    private function toRangeString(int $start, int $length): string
    {
        return sprintf('(%d, %d)', $start, $length);
    }
}
