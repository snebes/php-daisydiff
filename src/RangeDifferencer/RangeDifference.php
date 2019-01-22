<?php
/**
 * (c) Steve Nebes <snebes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

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
    protected $kind = 0;

    /** @var int */
    protected $leftStart = 0;

    /** @var int */
    protected $leftLength = 0;

    /** @var int */
    protected $rightStart = 0;

    /** @var int */
    protected $rightLength = 0;

    /** @var int */
    protected $ancestorStart = 0;

    /** @var int */
    protected $ancestorLength = 0;

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
        int $kind = 0,
        int $rightStart = 0,
        int $rightLength = 0,
        int $leftStart = 0,
        int $leftLength = 0,
        int $ancestorStart = 0,
        int $ancestorLength = 0
    ) {
        $this->kind = $kind;
        $this->rightStart = $rightStart;
        $this->rightLength = $rightLength;
        $this->leftStart = $leftStart;
        $this->leftLength = $leftLength;
        $this->ancestorStart = $ancestorStart;
        $this->ancestorLength = $ancestorLength;
    }

    /**
     * Returns the kind of difference.
     *
     * @return int
     */
    public function getKind(): int
    {
        return $this->kind;
    }

    /**
     * @param int $kind
     * @return self
     *
     * @codeCoverageIgnore
     */
    public function setKind(int $kind): self
    {
        $this->kind = $kind;
        return $this;
    }

    /**
     * Returns the start index of the entity range on the ancestor side.
     *
     * @return int
     */
    public function getAncestorStart(): int
    {
        return $this->ancestorStart;
    }

    /**
     * @param int $ancestorStart
     * @return self
     *
     * @codeCoverageIgnore
     */
    public function setAncestorStart(int $ancestorStart): self
    {
        $this->ancestorStart = $ancestorStart;
        return $this;
    }

    /**
     * Returns the number of entities on the ancestor side.
     *
     * @return int
     */
    public function getAncestorLength(): int
    {
        return $this->ancestorLength;
    }

    /**
     * @param int $ancestorLength
     * @return self
     *
     * @codeCoverageIgnore
     */
    public function setAncestorLength(int $ancestorLength): self
    {
        $this->ancestorLength = $ancestorLength;
        return $this;
    }

    /**
     * Returns the end index of the entity range on the ancestor side.
     *
     * @return int
     */
    public function getAncestorEnd(): int
    {
        return $this->ancestorStart + $this->ancestorLength;
    }

    /**
     * Returns the start index of the entity range on the right side.
     *
     * @return int
     */
    public function getRightStart(): int
    {
        return $this->rightStart;
    }

    /**
     * @param int $rightStart
     * @return self
     */
    public function setRightStart(int $rightStart): self
    {
        $this->rightStart = $rightStart;
        return $this;
    }

    /**
     * Returns the number of entities on the right side.
     *
     * @return int
     */
    public function getRightLength(): int
    {
        return $this->rightLength;
    }

    /**
     * @param int $rightLength
     * @return self
     */
    public function setRightLength(int $rightLength): self
    {
        $this->rightLength = $rightLength;
        return $this;
    }

    /**
     * Returns the end index of the entity range on the right side.
     *
     * @return int
     */
    public function getRightEnd(): int
    {
        return $this->rightStart + $this->rightLength;
    }

    /**
     * Returns the start index of the entity range on the left side.
     *
     * @return int
     */
    public function getLeftStart(): int
    {
        return $this->leftStart;
    }

    /**
     * @param int $leftStart
     * @return self
     */
    public function setLeftStart(int $leftStart): self
    {
        $this->leftStart = $leftStart;
        return $this;
    }

    /**
     * Returns the number of entities on the left side.
     *
     * @return int
     */
    public function getLeftLength(): int
    {
        return $this->leftLength;
    }

    /**
     * @param int $leftLength
     * @return self
     */
    public function setLeftLength(int $leftLength): self
    {
        $this->leftLength = $leftLength;
        return $this;
    }

    /**
     * Returns the end index of the entity range on the left side.
     *
     * @return int
     */
    public function getLeftEnd(): int
    {
        return $this->leftStart + $this->leftLength;
    }

    /**
     * Returns the maximum number of entities in the left, right, and ancestor sides of this range.
     *
     * @return int
     */
    public function getMaxLength(): int
    {
        return \max($this->rightLength, $this->leftLength, $this->ancestorLength);
    }

    /**
     * @param RangeDifference $other
     * @return bool
     */
    public function equals(RangeDifference $other): bool
    {
        return
            $this->kind === $other->getKind() &&
            $this->leftStart === $other->getLeftStart() &&
            $this->leftLength === $other->getLeftLength() &&
            $this->rightStart === $other->getRightStart() &&
            $this->rightLength === $other->getRightLength() &&
            $this->ancestorStart === $other->getAncestorStart() &&
            $this->ancestorLength === $other->getAncestorLength();
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        $str = \sprintf('Left: (%d, %d) Right: (%d, %d)',
            $this->getLeftStart(), $this->getLeftLength(),
            $this->getRightStart(), $this->getRightLength());

        if ($this->ancestorLength > 0 || $this->ancestorStart > 0) {
            $str .= \sprintf(' Ancestor: (%d, %d)', $this->getAncestorStart(), $this->getAncestorLength());
        }

        return $str;
    }
}
