<?php

declare(strict_types=1);

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
     * @param  TagNode $value
     * @return void
     */
    public function setLastCommonParent(?TagNode $value): void
    {
        $this->parent = $value;
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
     * @param  int $value
     * @return void
     */
    public function setLastCommonParentDepth(int $value): void
    {
        $this->lastCommonParentDepth = $value;
    }

    /**
     * @return int
     */
    public function getIndexInLastCommonParent(): int
    {
        return $this->indexInLastCommonParent;
    }

    /**
     * @param  int $value
     * @return void
     */
    public function setIndexInLastCommonParent(int $value): void
    {
        $this->indexInLastCommonParent = $value;
    }
}
