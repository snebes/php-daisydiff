<?php declare(strict_types=1);

namespace DaisyDiff\RangeDifferencer;

/**
 * Description of a change between two or three ranges of comparable entities.
 *
 * RangeDifference objects are the elements of a compare result returned from the RangeDifferencer find* methods.
 * Clients use these objects as they are returned from the differencer. This class is not intended to be instantiated or
 * subclassed outside of the Compare framework.
 *
 * Note: A range in the RangeDifference object is given as a start index and length in terms of comparable entities.
 * However, these entity indices and counts are not necessarily character positions. For example, if an entity
 * represents a line in a document, the start index would be a line number and the count would be in lines.
 */
class RangeDifference
{
    /** @var RangeDifferenceType */
    private $fKind;

    /** @var float */
    public $fLeftStart;

    /** @var float */
    protected $fLeftLength;

    /** @var float */
    public $fRightStart;

    /** @var float */
    protected $fRightLength;

    /** @var float */
    protected $lAncestorStart;

    /** @var float */
    protected $lAncestorLength;

    /**
     * @param  RangeDifferenceType $kind
     * @parma  float               $rightStart
     * @parma  float               $rightLength
     * @parma  float               $leftStart
     * @parma  float               $leftLength
     * @parma  float               $ancestorStart
     * @parma  float               $ancestorLength
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
        $this->fKind            = $kind;
        $this->fRightStart      = $rightStart;
        $this->fRightLength     = $rightLength;
        $this->fLeftStart       = $leftStart;
        $this->fLeftLength      = $leftLength;
        $this->lAncestorStart   = $ancestorStart;
        $this->lAncestorLength  = $ancestorLength;
    }

    /**
     * @return int
     */
    public function kind(): int
    {
        return $this->fKind;
    }

    /**
     * @return int
     */
    public function ancestorStart(): int
    {
        return $this->lAncestorStart;
    }

    /**
     * @return int
     */
    public function ancestorLength(): int
    {
        return $this->lAncestorLength;
    }

    /**
     * @return int
     */
    public function ancestorEnd(): int
    {
        return $this->lAncestorStart + $this->lAncestorLength;
    }

    /**
     * @return int
     */
    public function rightStart(): int
    {
        return $this->fRightStart;
    }

    /**
     * @return int
     */
    public function rightLength(): int
    {
        return $this->fRightLength;
    }

    /**
     * @return int
     */
    public function rightEnd(): int
    {
        return $this->fRightStart + $this->fRightLength;
    }

    /**
     * @return int
     */
    public function leftStart(): int
    {
        return $this->fLeftStart;
    }

    /**
     * @return int
     */
    public function leftLength(): int
    {
        return $this->fLeftLength;
    }

    /**
     * @return int
     */
    public function leftEnd(): int
    {
        return $this->fLeftStart + $this->fLeftLength;
    }

    /**
     * @return int
     */
    public function maxLength(): int
    {
        return max($this->fRightLength, $this->fLeftLength, $this->lAncestorLength);
    }

    /**
     * @param  RangeDifference $other
     * @return bool
     */
    public function equals(RangeDifference $other): bool
    {
        return
            $this->fKind == $other->kind() &&
            $this->fLeftStart == $other->leftStart() &&
            $this->fLeftLength == $other->leftLength() &&
            $this->fRigthStart == $other->rightStart() &&
            $this->fRightLength == $other->rightLength() &&
            $this->lAncestorStart == $other->ancestorStart() &&
            $this->lAncestorLength == $other->ancestorLength()
        ;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        $str = sprintf('Left: %s Right: %s',
            $this->toRangeString($this->fLeftStart, $this->fLeftLength),
            $this->toRangeString($this->fRightStart, $this->fRightLength)
        );

        if ($this->lAncestorLength > 0 || $this->lAncestorStart > 0) {
            $str .= sprintf(' Ancestor: %s', $this->toRangeString($this->lAncestorStart, $this->lAncestorLength));
        }

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
