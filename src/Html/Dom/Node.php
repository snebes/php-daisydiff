<?php
/**
 * (c) Steve Nebes <snebes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace DaisyDiff\Html\Dom;

use DaisyDiff\Html\Dom\Helper\LastCommonParentResult;

/**
 * Represents any element in the DOM tree of a HTML file.
 */
abstract class Node
{
    /** @var TagNode|null */
    protected $parent;

    /** @var TagNode */
    private $root;

    /** @var bool */
    private $whiteBefore = false;

    /** @var bool */
    private $whiteAfter = false;

    /**
     * This constructor not only sets the parameter as the parent for the created node, but also appends the created
     * node to the collection of parent's children.
     *
     * @param TagNode|null $parent
     */
    public function __construct(?TagNode $parent)
    {
        $this->parent = $parent;

        if (null !== $parent) {
            $parent->addChild($this);
            $this->root = $parent->getRoot();
        } elseif ($this instanceof TagNode) {
            $this->root = $this;
        }
    }

    /**
     * Get the parent node instance.
     *
     * @return TagNode|null
     */
    public function getParent(): ?TagNode
    {
        return $this->parent;
    }

    /**
     * Returns a list of the ancestors that is ordered starting from the root by the depth. Index of an element in that
     * list corresponds its depth (if depth of the root is 0).
     *
     * @return TagNode[]
     */
    public function getParentTree(): array
    {
        /** @var TagNode[] $ancestors */
        $ancestors = [];

        for ($ancestor = $this->getParent(); null !== $ancestor; $ancestor = $ancestor->getParent()) {
            $ancestors[] = $ancestor;
        }

        return \array_reverse($ancestors);
    }

    /**
     * "equals" method should work differently for the case where the compared nodes are from the same tree, and in that
     * case return true only if it's the same object.
     *
     * This method returns the root of the tree (which should be common ancestor for every node in the tree). If the
     * roots are the same object, then the nodes are in the same tree.
     *
     * Returns the "top" ancestor if this node has a parent, or the node itself if there is no parent, and this is a
     * TagNode or null if there is no parents and this node isn't a TagNode.
     *
     * @return TagNode
     */
    public function getRoot(): TagNode
    {
        return $this->root;
    }

    /**
     * @param int $id
     * @return Node[]
     */
    abstract public function getMinimalDeletedSet(int $id): array;

    /**
     * Descent the ancestors list for both nodes stopping either at the first no-match case or when either of the lists
     * is exhausted.
     *
     * @param Node $other
     * @return LastCommonParentResult
     */
    public function getLastCommonParent(Node $other): LastCommonParentResult
    {
        $result = new LastCommonParentResult();

        // Note: these lists are never null, but sometimes are empty.
        $myParents = $this->getParentTree();
        $otherParents = $other->getParentTree();

        $myParentsCount = \count($myParents);
        $otherParentsCount = \count($otherParents);

        $i = 1;
        $isSame = true;

        while ($isSame && $i < $myParentsCount && $i < $otherParentsCount) {
            if (!$myParents[$i]->isSameTag($otherParents[$i])) {
                $isSame = false;
            } else {
                // After the while, the index $i-1 must be the last common parent.
                $i++;
            }
        }

        $result->setLastCommonParentDepth($i - 1);
        $result->setLastCommonParent($myParents[$i - 1]);

        if (!$isSame) {
            // Found different parent.
            $result->setIndexInLastCommonParent($myParents[$i - 1]->getIndexOf($myParents[$i]));
            $result->setSplittingNeeded();
        } elseif ($myParentsCount < $otherParentsCount) {
            // Current node is not so deeply nested.
            $result->setIndexInLastCommonParent($myParents[$i - 1]->getIndexOf($this));
        } elseif ($myParentsCount > $otherParentsCount) {
            // All tags matched but there are tags left in this tree - other node is not so deeply nested.
            $result->setIndexInLastCommonParent($myParents[$i - 1]->getIndexOf($myParents[$i]));
            $result->setSplittingNeeded();
        } else {
            // All tags matched until the very last one in both trees or there were not tags besides the BODY.
            $result->setIndexInLastCommonParent($myParents[$i - 1]->getIndexOf($this));
        }

        return $result;
    }

    /**
     * Changes the $parent field of this node. Does NOT append/remove itself from the previous or the new parent
     * children collection.
     *
     * @param TagNode|null $parent
     */
    public function setParent(?TagNode $parent): void
    {
        $this->parent = $parent;

        if (null !== $parent) {
            $this->setRoot($parent->getRoot());
        }
    }

    /**
     * @param TagNode $root
     */
    protected function setRoot(TagNode $root): void
    {
        $this->root = $root;
    }

    /**
     * Return a deep copy of this node tree.
     *
     * @return Node
     */
    abstract public function copyTree(): Node;

    /**
     * Return true only if one of the ancestors is a <pre> tag. False otherwise, including the case where this node is
     * a <pre> tag.
     *
     * @return bool
     */
    public function inPre(): bool
    {
        foreach ($this->getParentTree() as $ancestor) {
            if ($ancestor instanceof TagNode && $ancestor->isPre()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function isWhiteBefore(): bool
    {
        return $this->whiteBefore;
    }

    /**
     * @param bool $value
     */
    public function setWhiteBefore(bool $value): void
    {
        $this->whiteBefore = $value;
    }

    /**
     * @return bool
     */
    public function isWhiteAfter(): bool
    {
        return $this->whiteAfter;
    }

    /**
     * @param bool $value
     */
    public function setWhiteAfter(bool $value): void
    {
        $this->whiteAfter = $value;
    }

    /**
     * @return Node
     */
    abstract public function getLeftMostChild(): Node;

    /**
     * @return Node
     */
    abstract public function getRightMostChild(): Node;

    /**
     * @return string
     */
    abstract public function __toString(): string;
}
