<?php declare(strict_types=1);

namespace DaisyDiff\Html\Ancestor;

use DaisyDiff\Html\Dom\TagNode;
use DaisyDiff\RangeDifferencer\RangeComparatorInterface;
use DaisyDiff\RangeDifferencer\RangeDifference;
use DaisyDiff\RangeDifferencer\RangeDifferencer;
use OutOfBoundsException;

/**
 * A comparator used when calculating the difference in ancestry of two Nodes.
 */
class AncestorComparator implements RangeComparatorInterface
{
    /** @var TagNode[] */
    private $ancestors = [];

    /** @var string */
    private $compareTxt = '';

    /**
     * @param  TagNode[] $ancestors
     */
    public function __construct(?array $ancestors)
    {
        $this->ancestors = $ancestors ?? [];
    }

    /**
     * @return int
     */
    public function getRangeCount(): int
    {
        return count($this->ancestors);
    }

    /**
     * @param  int                      $thisIndex
     * @param  RangeComparatorInterface $other
     * @param  int                      $otherIndex
     * @return bool
     */
    public function rangesEqual(int $thisIndex, RangeComparatorInterface $other, int $otherIndex): bool
    {
        if ($other instanceof AncestorComparator) {
            return $other->getAncestor($otherIndex)->isSameTag($this->getAncestor($thisIndex));
        }

        return false; // @codeCoverageIgnore
    }

    /**
     * @param  int                      $length
     * @param  int                      $maxLength
     * @param  RangeComparatorInterface $other
     * @return bool
     */
    public function skipRangeComparison(int $length, int $maxLength, RangeComparatorInterface $other): bool
    {
        return false;
    }

    /**
     * @param  int $i
     * @return TagNode|null
     * @throws OutOfBoundsException
     */
    public function getAncestor(int $i): ?TagNode
    {
        if (array_key_exists($i, $this->ancestors)) {
            return $this->ancestors[$i];
        } else {
            throw new OutOfBoundsException(sprintf('Index: %d, Size: %d', $i, count($this->ancestors)));
        }
    }

    /**
     * @return string
     */
    public function getCompareTxt(): string
    {
        return $this->compareTxt;
    }

    /**
     * @param  AncestorComparator $other
     * @return AncestorComparatorResult
     */
    public function getResult(AncestorComparator $other): AncestorComparatorResult
    {
        $result = new AncestorComparatorResult();

        /** @var RangeDifference[] */
        $differences = RangeDifferencer::findDifferences($other, $this);

        if (0 == count($differences)) {
            return $result;
        }

        $changeTxt = new ChangeTextGenerator($this, $other);

        $result->setChanged(true);
        $result->setChanges(strval($changeTxt->getChanged($differences)));
        $result->setHtmlLayoutChanges($changeTxt->getHtmlLayoutChanges());

        return $result;
    }
}
