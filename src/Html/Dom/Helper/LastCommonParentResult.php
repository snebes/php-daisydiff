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
     * @return self
     */
    public function setLastCommonParent(TagNode $parent): self
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return bool
     */
    public function isSplittingNeeded(): bool
    {
        return $this->splittingNeeded;
    }

    /**
     * @return self
     */
    public function setSplittingNeeded(): self
    {
        $this->splittingNeeded = true;

        return $this;
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
     * @return self
     */
    public function setLastCommonParentDepth(int $depth): self
    {
        $this->lastCommonParentDepth = $depth;

        return $this;
    }

    /**
     * @return int
     */
    public function getIndexInLastCommonParent(): int
    {
        return $this->indexInLastCommonParent;
    }

    /**
     * @param  int $depth
     * @return self
     */
    public function setIndexInLastCommonParent(int $index): self
    {
        $this->indexInLastCommonParent = $index;

        return $this;
    }
}
