<?php declare(strict_types=1);

namespace DaisyDiff\Html;

use ArrayIterator;
use DaisyDiff\Html\Ancestor\AncestorComparator;
use DaisyDiff\Html\Ancestor\AncestorComparatorResult;
use DaisyDiff\Html\Dom\BodyNode;
use DaisyDiff\Html\Dom\DomTreeInterface;
use DaisyDiff\Html\Dom\Helper\LastCommonParentResult;
use DaisyDiff\Html\Dom\TagNode;
use DaisyDiff\Html\Dom\TextNode;
use DaisyDiff\Html\Modification\Modification;
use DaisyDiff\Html\Modification\ModificationType;
use DaisyDiff\RangeDifferencer\RangeComparatorInterface;
use IteratorAggregate;
use RuntimeException;

/**
 * A comparator that generates a DOM tree of sorts from handling SAX events. Then it can be used to compute the
 * differences between DOM trees and mark elements accordingly.
 */
class TextNodeComparator implements RangeComparatorInterface, IteratorAggregate
{
    /** @var TextNode[] */
    private $textNodes = [];

    /** @var Modification[] */
    private $lastModified = [];

    /** @var BodyNode */
    private $bodyNode;

    /** @var int */
    private $newId = 0;

    /** @var int */
    private $changedId = 0;

    /** @var bool */
    private $changedIdUsed = false;

    /** @var bool */
    private $whiteAfterLastChangedPart = false;

    /** @var int */
    private $deletedId = 0;

    /**
     * @param DomTreeInterface $tree
     */
    public function __construct(DomTreeInterface $tree)
    {
        $this->textNodes = $tree->getTextNodes();
        $this->bodyNode  = $tree->getBodyNode();
    }

    /**
     * @return BodyNode
     */
    public function getBodyNode(): BodyNode
    {
        return $this->bodyNode;
    }

    /**
     * @return int
     */
    public function getRangeCount(): int
    {
        return count($this->textNodes);
    }

    /**
     * @param  int $i
     * @return TextNode|null
     */
    public function getTextNode(int $i): ?TextNode
    {
        return $this->textNodes[$i] ?? null;
    }

    /**
     * Marks the given range as new. In the output, the range will be formatted as specified by the anOutputFormat
     * parameter.
     *
     * @param  int    $start
     * @param  int    $end
     * @param  string $outputFormat
     * @return void
     */
    public function markAsNew(int $start, int $end, string $outputFormat = ModificationType::ADDED): void
    {
        if ($end <= $start) {
            return;
        }

        if ($this->whiteAfterLastChangedPart) {
            $this->getTextNode($start)->setWhiteBefore(false);
        }

        /** @var Modification[] */
        $nextLastModified = [];

        for ($i = $start; $i < $end; $i++) {
            $mod = new Modification(ModificationType::ADDED, $outputFormat);
            $mod->setId($this->newId);

            if (count($this->lastModified) > 0) {
                $mod->setPrevious($this->lastModified[0]);

                if (null === $this->lastModified[0]->getNext()) {
                    foreach ($this->lastModified as $lastMod) {
                        $lastMod->setNext($mod);
                    }
                }
            }

            $nextLastModified[] = $mod;
            $this->getTextNode($i)->setModification($mod);
        }

        $this->getTextNode($start)->getModification()->setFirstOfId(true);
        $this->newId++;
        $this->lastModified = $nextLastModified;
    }

    /**
     * @param  int                      $thisIndex
     * @param  RangeComparatorInterface $other
     * @param  int                      $otherIndex
     * @return bool
     */
    public function rangesEqual(int $thisIndex, RangeComparatorInterface $other, int $otherIndex): bool
    {
        if ($other instanceof TextNodeComparator) {
            return $this->getTextNode($thisIndex)->isSameText($other->getTextNode($otherIndex));
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
     * @param  int                $leftStart
     * @param  int                $leftEnd
     * @param  int                $rightStart
     * @param  int                $rightEnd
     * @param  TextNodeComparator $leftComparator
     * @return void
     */
    public function handlePossibleChangedPart(
        int $leftStart = 0,
        int $leftEnd = 0,
        int $rightStart = 0,
        int $rightEnd = 0,
        TextNodeComparator $leftComparator
    ): void {
        assert(is_int($leftEnd));

        $i = $rightStart;
        $j = $leftStart;

        if ($this->changedIdUsed) {
            $this->changedId++;
            $this->changedIdUsed = false;
        }

        /** @var Modification[] */
        $nextLastModified = [];
        $changes = null;

        while ($i < $rightEnd) {
            $acThis  = new AncestorComparator($this->getTextNode($i)->getParentTree());
            $acOther = new AncestorComparator($leftComparator->getTextNode($j)->getParentTree());

            /** @var AncestorComparatorResult */
            $result = $acThis->getResult($acOther);

            if ($result->isChanged()) {
                $mod = new Modification(ModificationType::CHANGED, ModificationType::CHANGED);

                if (!$this->changedIdUsed) {
                    $mod->setFirstOfId(true);

                    if (count($nextLastModified) > 0) {
                        $this->lastModified = $nextLastModified;
                        $nextLastModified = [];
                    }
                }
                elseif (null !== $result->getChanges() && $result->getChanges() != $changes) {
                    $this->changedId++;
                    $mod->setFirstOfId(true);

                    if (count($nextLastModified) > 0) {
                        $this->lastModified = $nextLastModified;
                        $nextLastModified = [];
                    }
                }

                if (count($this->lastModified) > 0) {
                    $mod->setPrevious($this->lastModified[0]);

                    if (null === $this->lastModified[0]->getNext()) {
                        foreach ($this->lastModified as $lastMod) {
                            $lastMod->setNext($mod);
                        }
                    }
                }

                $nextLastModified[] = $mod;

                $mod->setChanges($result->getChanges());
                $mod->setHtmlLayoutChanges($result->getHtmlLayoutChanges());
                $mod->setId($this->changedId);

                $this->getTextNode($i)->setModification($mod);
                $changes = $result->getChanges();
                $this->changedIdUsed = true;
            }
            elseif ($this->changedIdUsed) {
                $this->changedId++;
                $this->changedIdUsed = false;
            }

            $i++;
            $j++;
        }

        if (count($nextLastModified) > 0) {
            $this->lastModified = $nextLastModified;
        }
    }

    /**
     * Marks the given range as deleted. In the output, the range will be formatted as specified by the parameter
     * anOutputFormat.
     *
     * @param  int                $start
     * @param  int                $end
     * @param  TextNodeComparator $oldComp
     * @param  int                $before
     * @param  int                $after
     * @param  string             $outputFormat
     * @return void
     */
    public function markAsDeleted(
        int $start,
        int $end,
        TextNodeComparator $oldComp,
        int $before,
        int $after = 0,
        string $outputFormat = ModificationType::REMOVED
    ): void {
        if ($end <= $start) {
            return;
        }

        if ($before > 0 && $this->getTextNode($before - 1)->isWhiteAfter()) {
            $this->whiteAfterLastChangedPart = true;
        } else {
            $this->whiteAfterLastChangedPart = false;
        }

        /** @var Modification[] */
        $nextLastModified = [];

        for ($i = $start; $i < $end; $i++) {
            $mod = new Modification(ModificationType::REMOVED, $outputFormat);
            $mod->setId($this->deletedId);

            if (count($this->lastModified) > 0) {
                $mod->setPrevious($this->lastModified[0]);

                if (null === $this->lastModified[0]->getNext()) {
                    foreach ($this->lastModified as $lastMod) {
                        $lastMod->setNext($mod);
                    }
                }
            }

            $nextLastModified[] = $mod;

            // $oldComp is used here because we're going to move its deleted elements to this tree.
            $oldComp->getTextNode($i)->setModification($mod);
        }

        $oldComp->getTextNode($start)->getModification()->setFirstOfId(true);

        /** @var TagNode[] $deletedNodes */
        $deletedNodes = $oldComp->getBodyNode()->getMinimalDeletedSet($this->deletedId);

        // Set $prevLeaf to the leaf after which the old HTML needs to be inserted.
        $prevLeaf = null;

        if ($before > 0) {
            $prevLeaf = $this->getTextNode($before - 1);
        }

        // Set $nextLeaf to the leaf before which the old HTML needs to be inserted.
        $nextLeaf = null;
        $useAfter = false;

        if ($after < $this->getRangeCount()) {
            $orderResult = $this->getTextNode($before)->getLastCommonParent($this->getTextNode($after));
            $check = $this->getTextNode($before)->getParentTree();
            $check = array_reverse($check);

            foreach ($check as $curr) {
                if ($curr === $orderResult->getLastCommonParent()) {
                    break;
                }
                elseif ($curr->isBlockLevel()) {
                    $useAfter = true;
                    break;
                }
            }

            if (!$useAfter) {
                $check = $this->getTextNode($after)->getParentTree();
                $check = array_reverse($check);

                foreach ($check as $curr) {
                    if ($curr === $orderResult->getLastCommonParent()) {
                        break;
                    }
                    elseif ($curr->isBlockLevel()) {
                        $useAfter = true;
                        break;
                    }
                }
            }
        } else {
            $useAfter = false;
        }

        if ($useAfter) {
            $nextLeaf = $this->getTextNode($after);
        }
        elseif ($before < $this->getRangeCount()) {
            $nextLeaf = $this->getTextNode($before);
        }

        while (count($deletedNodes) > 0) {
            $prevResult = null;
            $nextResult = null;

            if (null !== $prevLeaf) {
                $prevResult = $prevLeaf->getLastCommonParent($deletedNodes[0]);
            } else {
                $prevResult = new LastCommonParentResult();
                $prevResult->setLastCommonParent($this->getBodyNode());
                $prevResult->setIndexInLastCommonParent(-1);
            }

            if (null !== $nextLeaf) {
                $nextResult = $nextLeaf->getLastCommonParent($deletedNodes[count($deletedNodes) - 1]);
            } else {
                $nextResult = new LastCommonParentResult();
                $nextResult->setLastCommonParent($this->getBodyNode());
                $nextResult->setIndexInLastCommonParent($this->getBodyNode()->getNumChildren());
            }

            if ($prevResult->getLastCommonParentDepth() == $nextResult->getLastCommonParentDepth()) {
                // We need some metric to choose which way to add...
                if ($deletedNodes[0]->getParent() === $deletedNodes[count($deletedNodes) - 1]->getParent() &&
                    $prevResult->getLastCommonParent() === $nextResult->getLastCommonParent()) {
                    // The difference is not in the parent.
                    $prevResult->setLastCommonParentDepth($prevResult->getLastCommonParentDepth() + 1);
                } else {
                    // The difference is in the parent, so compare them. now THIS is tricky.
                    $distancePrev = $deletedNodes[0]->getParent()->getMatchRatio($prevResult->getLastCommonParent());
                    $distanceNext = $deletedNodes[count($deletedNodes) - 1]->getParent()
                        ->getMatchRatio($nextResult->getLastCommonParent());

                    if ($distancePrev <= $distanceNext) {
                        // Insert after the previous node.
                        $prevResult->setLastCommonParentDepth($prevResult->getLastCommonParentDepth() + 1);
                    } else {
                        // Isert before the next node.
                        $nextResult->setLastCommonParentDepth($nextResult->getLastCommonParentDepth() + 1);
                    }
                }
            }

            if ($prevResult->getLastCommonParentDepth() > $nextResult->getLastCommonParentDepth()) {
                // Inserting at the front.
                if ($prevResult->isSplittingNeeded()) {
                    $prevLeaf->getParent()->splitUntil($prevResult->getLastCommonParent(), $prevLeaf, true);
                }

                // array_shift removes first array element, and returns it.
                $prevLeaf = (array_shift($deletedNodes))->copyTree();
                $prevLeaf->setParent($prevResult->getLastCommonParent());
                $prevResult->getLastCommonParent()->addChild($prevLeaf, $prevResult->getIndexInLastCommonParent() + 1);
            }
            elseif ($prevResult->getLastCommonParentDepth() < $nextResult->getLastCommonParentDepth()) {
                // Inserting at the back.
                if ($nextResult->isSplittingNeeded()) {
                    $splitOccurred = $nextLeaf->getParent()
                        ->splitUntil($nextResult->getLastCommonParent(), $nextLeaf, false);

                    if ($splitOccurred) {
                        // The place where to insert is shifted one place to the right.
                        $nextResult->setIndexInLastCommonParent($nextResult->getIndexInLastCommonParent() + 1);
                    }
                }

                // array_pop removes last array element, and returns it.
                $nextLeaf = (array_pop($deletedNodes))->copyTree();
                $nextLeaf->setParent($nextResult->getLastCommonParent());
                $nextResult->getLastCommonParent()->addChild($nextLeaf, $nextResult->getIndexInLastCommonParent());
            }
            else {
                throw new RuntimeException();
            }
        }

        $this->lastModified = $nextLastModified;
        $this->deletedId++;
    }

    /**
     * @return void
     */
    public function expandWhiteSpace(): void
    {
        $this->getBodyNode()->expandWhiteSpace();
    }

    /**
     * @return ArrayIterator
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->textNodes);
    }

    /**
     * Used for combining multiple comparators in order to create a single output document. The IDs must be successive
     * along the different comparators.
     *
     * @param  int $value
     * @return void
     */
    public function setStartDeletedId(int $value): void
    {
        $this->deletedId = $value;
    }

    /**
     * Used for combining multiple comparators in order to create a single output document. The IDs must be successive
     * along the different comparators.
     *
     * @param  int $value
     * @return void
     */
    public function setStartChangedID(int $value): void
    {
        $this->changedId = $value;
    }

    /**
     * Used for combining multiple comparators in order to create a single output document. The IDs must be successive
     * along the different comparators.
     *
     * @param  int $value
     * @return void
     */
    public function setStartNewID(int $value): void
    {
        $this->newId = $value;
    }

    /**
     * @return int
     */
    public function getChangedId(): int
    {
        return $this->changedId;
    }

    /**
     * @return int
     */
    public function getDeletedId(): int
    {
        return $this->deletedId;
    }

    /**
     * @return int
     */
    public function getNewId(): int
    {
        return $this->newId;
    }

    /**
     * @return Modification[]
     */
    public function getLastModified(): array
    {
        return $this->lastModified;
    }

    /**
     * @param  Modification[] $lastModified
     * @return void
     */
    public function setLastModified(?array $lastModified): void
    {
        $this->lastModified = $lastModified ?? [];
    }
}
