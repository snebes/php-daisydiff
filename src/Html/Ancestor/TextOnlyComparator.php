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
use SN\DaisyDiff\Html\Dom\TextNode;
use SN\RangeDifferencer\Core\LCSSettings;
use SN\RangeDifferencer\RangeComparatorInterface;
use SN\RangeDifferencer\RangeDifferencer;

/**
 * A comparator that compares only the elements of text inside a given tag.
 */
class TextOnlyComparator implements RangeComparatorInterface
{
    /** @var TextNode[] */
    private $leafs = [];

    /**
     * Default values.
     *
     * @param TagNode $tree
     */
    public function __construct(TagNode $tree)
    {
        $this->addRecursive($tree);
    }

    /**
     * @param TagNode $tree
     */
    private function addRecursive(TagNode $tree): void
    {
        foreach ($tree as $child) {
            if ($child instanceof TagNode) {
                $this->addRecursive($child);
            } elseif ($child instanceof TextNode) {
                $this->leafs[] = $child;
            }
        }
    }

    /**
     * @return int
     */
    public function getRangeCount(): int
    {
        return \count($this->leafs);
    }

    /**
     * {@inheritdoc}
     */
    public function rangesEqual(int $thisIndex, RangeComparatorInterface $other, int $otherIndex): bool
    {
        if ($other instanceof TextOnlyComparator) {
            return $this->getLeaf($thisIndex)->isSameText($other->getLeaf($otherIndex));
        }

        return false; // @codeCoverageIgnore
    }

    /**
     * @param int $index
     * @return TextNode
     *
     * @throws \OutOfBoundsException
     */
    public function getLeaf(int $index): TextNode
    {
        if (isset($this->leafs[$index])) {
            return $this->leafs[$index];
        }

        throw new \OutOfBoundsException(\sprintf('Index: %d, Size: %d', $index, \count($this->leafs)));
    }

    /**
     * {@inheritdoc}
     */
    public function skipRangeComparison(int $length, int $maxLength, RangeComparatorInterface $other): bool
    {
        return false;
    }

    /**
     * @param TextOnlyComparator $other
     * @return float
     */
    public function getMatchRatio(TextOnlyComparator $other): float
    {
        $settings = new LCSSettings();
        $settings->setUseGreedyMethod(true);
        $settings->setPowLimit(1.5);
        $settings->setTooLong(150 * 150);

        $differences = RangeDifferencer::findDifferences($other, $this, $settings);
        $distanceOther = 0;
        $distanceThis = 0;

        foreach ($differences as $d) {
            $distanceOther += $d->getLeftLength();
            $distanceThis += $d->getRightLength();
        }

        return (float) ((0.0 + $distanceOther) / $other->getRangeCount() +
                (0.0 + $distanceThis) / $this->getRangeCount()) / 2;
    }
}
