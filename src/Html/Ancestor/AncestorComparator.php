<?php
/**
 * (c) Steve Nebes <snebes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace SN\DaisyDiff\Html\Ancestor;

use SN\DaisyDiff\Html\Dom\TagNode;
use SN\DaisyDiff\RangeDifferencer\RangeComparatorInterface;
use SN\DaisyDiff\RangeDifferencer\RangeDifference;
use SN\DaisyDiff\RangeDifferencer\RangeDifferencer;

/**
 * A comparator used when calculating the difference in ancestry of two Nodes.
 */
class AncestorComparator implements RangeComparatorInterface
{
    /** @var TagNode[] */
    private $ancestors = [];

    /** @var string */
    private $compareText = '';

    /**
     * @param TagNode[] $ancestors
     */
    public function __construct(array $ancestors)
    {
        $this->ancestors = $ancestors;
    }

    /**
     * @return int
     */
    public function getRangeCount(): int
    {
        return \count($this->ancestors);
    }

    /**
     * {@inheritdoc}
     */
    public function rangesEqual(int $thisIndex, RangeComparatorInterface $other, int $otherIndex): bool
    {
        if ($other instanceof AncestorComparator) {
            return $other->getAncestor($otherIndex)->isSameTag($this->getAncestor($thisIndex));
        }

        return false; // @codeCoverageIgnore
    }

    /**
     * {@inheritdoc}
     */
    public function skipRangeComparison(int $length, int $maxLength, RangeComparatorInterface $other): bool
    {
        return false;
    }

    /**
     * @param int $index
     * @return TagNode|null
     *
     * @throws \OutOfBoundsException
     */
    public function getAncestor(int $index): ?TagNode
    {
        if (isset($this->ancestors[$index])) {
            return $this->ancestors[$index];
        }

        throw new \OutOfBoundsException(\sprintf('Index: %d, Size: %d', $index, \count($this->ancestors)));
    }

    /**
     * @return string
     */
    public function getCompareTxt(): string
    {
        return $this->compareText;
    }

    /**
     * @param AncestorComparator $other
     * @return AncestorComparatorResult
     */
    public function getResult(AncestorComparator $other): AncestorComparatorResult
    {
        $result = new AncestorComparatorResult();

        /** @var RangeDifference[] */
        $differences = RangeDifferencer::findDifferences($other, $this);

        if (empty($differences)) {
            return $result;
        }

        $changeText = new ChangeTextGenerator($this, $other);

        $result->setChanged(true);
        $result->setChanges($changeText->getChanged($differences)->getText());
        $result->setHtmlLayoutChanges($changeText->getHtmlLayoutChanges());

        return $result;
    }
}
