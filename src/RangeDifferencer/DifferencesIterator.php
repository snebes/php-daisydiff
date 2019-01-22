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
 * A custom iterator to iterate over a List of RangeDifferences.
 * It is used internally by the RangeDifferencer.
 *
 * @internal
 */
class DifferencesIterator
{
    /** @var RangeDifference[] */
    private $fRange = [];

    /** @var int */
    private $fIndex = 0;

    /** @var RangeDifference[] */
    private $fArray = [];

    /** @var RangeDifference */
    private $fDifference;

    /**
     * Default values.
     *
     * @param RangeDifference[] $differenceRanges
     */
    public function __construct(array $differenceRanges)
    {
        $this->fArray = $differenceRanges;
        $this->fIndex = 0;
        $this->fRange = [];

        if ($this->fIndex < \count($this->fArray)) {
            $this->fDifference = $this->fArray[$this->fIndex++];
        } else {
            $this->fDifference = null;
        }
    }

    /**
     * @return int
     */
    public function getCount(): int
    {
        return \count($this->fRange);
    }

    /**
     * @return RangeDifference|null
     */
    public function getDifference(): ?RangeDifference
    {
        return $this->fDifference;
    }

    /**
     * @return int
     */
    public function getIndex(): int
    {
        return $this->fIndex;
    }

    /**
     * @return array
     */
    public function getRange(): array
    {
        return $this->fRange;
    }

    /**
     * Appends the edit to its list and moves to the next RangeDifference.
     */
    public function next(): void
    {
        $this->fRange[] = $this->fDifference;

        if (null !== $this->fDifference) {
            if ($this->fIndex < \count($this->fArray)) {
                $this->fDifference = $this->fArray[$this->fIndex++];
            } else {
                $this->fDifference = null;
            }
        }
    }

    /**
     * @param DifferencesIterator $right
     * @param DifferencesIterator $left
     * @return DifferencesIterator
     */
    public function other(DifferencesIterator $right, DifferencesIterator $left): DifferencesIterator
    {
        if ($this === $right) {
            return $left;
        }

        return $right;
    }

    /**
     * @return void
     */
    public function removeAll(): void
    {
        $this->fRange = [];
    }
}

