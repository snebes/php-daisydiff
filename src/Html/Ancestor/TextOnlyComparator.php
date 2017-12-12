<?php declare(strict_types=1);

namespace DaisyDiff\Html\Ancestor;

use DaisyDiff\Html\Dom\TagNode;
use DaisyDiff\Html\Dom\TextNode;
use DaisyDiff\RangeDifferencer\LCSSettings;
use DaisyDiff\RangeDifferencer\RangeComparatorInterface;
use DaisyDiff\RangeDifferencer\RangeDifferencer;
use OutOfBoundsException;

/**
 * A comparator that compares only the elements of text inside a given tag.
 */
class TextOnlyComparator implements RangeComparatorInterface
{
    /** @var TextNode[] */
    private $leafs = [];

    /**
     * @param  TagNode $tree
     */
    public function __construct(TagNode $tree)
    {
        $this->addRecursive($tree);
    }

    /**
     * @param  TagNode $tree
     * @return void
     */
    private function addRecursive(TagNode $tree): void
    {
        foreach ($tree as $child) {
            if ($child instanceof TagNode) {
                $this->addRecursive($child);
            }
            elseif ($child instanceof TextNode) {
                $this->leafs[] = $child;
            }
        }
    }

    /**
     * @return int
     */
    public function getRangeCount(): int
    {
        return count($this->leafs);
    }

    /**
     * @param  int                      $owni
     * @param  RangeComparatorInterface $other
     * @param  int                      $otheri
     * @return bool
     */
    public function rangesEqual(int $owni, RangeComparatorInterface $other, int $otheri): bool
    {
        if (!$other instanceof TextOnlyComparator) {
            return false; // @codeCoverageIgnore
        }

        return $this->getLeaf($owni)->isSameText($other->getLeaf($otheri));
    }

    /**
     * @param  int $index
     * @return TextNode
     *
     * @throws OutOfBoundsException
     */
    public function getLeaf(int $index): TextNode
    {
        if (array_key_exists($index, $this->leafs)) {
            return $this->leafs[$index];
        } else {
            throw new OutOfBoundsException(sprintf('Index: %d, Size: %d', $index, count($this->leafs)));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function skipRangeComparison(int $length, int $maxLength, RangeComparatorInterface $other): bool
    {
        return false;
    }

    /**
     * @param  TextOnlyComparator $other
     * @return float
     */
    public function getMatchRatio(TextOnlyComparator $other): float
    {
        $settings = new LCSSettings();
        $settings->setGreedyMethod(true);
        $settings->setPowLimit(1.5);
        $settings->setTooLong(150 * 150);

        $differences   = RangeDifferencer::findDifferences($settings, $other, $this);
        $distanceOther = 0;
        $distanceThis  = 0;

        foreach ($differences as $d) {
            $distanceOther += $d->leftLength();
        }

        foreach ($differences as $d) {
            $distanceThis += $d->rightLength();
        }

        return floatval(((0.0 + $distanceOther) / $other->getRangeCount() + (0.0 + $distanceThis) / $this->getRangeCount()) / 2.0);
    }
}
