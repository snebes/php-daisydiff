<?php declare(strict_types=1);

namespace DaisyDiff\Html\Dom\Helper;

use DaisyDiff\Html\Dom\TagNode;

/**
 * When detecting the last common parent of two nodes, all results are stored as a LastCommonParentResult.
 */
class LastCommonParentResult
{
    /** @var TagNode */
    private $parent;

    /** @var bool */
    private $splittingNeeded = false;

    /** @var int */
    private $lastCommonParentDepth = -1;

    /** @var int */
    private $indexInLastCommonParent = -1;

    /**
     * @return TagNode|null
     */
    public function getLastCommonParent(): ?TagNode
    {
        return $this->parent;
    }

    /**
     * @param  TagNode $parent
     * @return void
     */
    public function setLastCommonParent(?TagNode $parent): void
    {
        $this->parent = $parent;
    }

    /**
     * @return bool
     */
    public function isSplittingNeeded(): bool
    {
        return $this->splittingNeeded;
    }

    /**
     * @return void
     */
    public function setSplittingNeeded(): void
    {
        $this->splittingNeeded = true;
    }

    /**
     * @return int
     */
    public function getLastCommonParentDepth(): int
    {
        return $this->lastCommonParentDepth;
    }

    /**
     * @param  int $depth
     * @return void
     */
    public function setLastCommonParentDepth(int $depth): void
    {
        $this->lastCommonParentDepth = $depth;
    }

    /**
     * @return int
     */
    public function getIndexInLastCommonParent(): int
    {
        return $this->indexInLastCommonParent;
    }

    /**
     * @param  int $index
     * @return void
     */
    public function setIndexInLastCommonParent(int $index): void
    {
        $this->indexInLastCommonParent = $index;
    }
}
