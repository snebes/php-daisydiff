<?php declare(strict_types=1);

namespace DaisyDiff\Html\Dom;

use DaisyDiff\Html\Dom\Helper\LastCommonParentResult;

/**
 * Represents any element in the DOM tree of an HTML file.
 */
abstract class Node
{
    /** @var TagNode */
    protected $parent;

    /** @var TagNode */
    protected $root;

    /** @var bool */
    private $whiteBefore = false;

    /** @var bool */
    private $whiteAfter = false;

    /**
     * This constructor not only sets the parameter as the parent for the created node, but also appends the created
     * node to the collection of parant's children.
     *
     * @param  Node $parent
     */
    public function __construct(?Node $parent)
    {
        $this->parent = $parent;

        if (!is_null($parent)) {
            $parent->addChild($this);
            $this->root = $parent->getRoot();
        }
        elseif ($this instanceof TagNode) {
            $this->root = $this;
        }
    }

    /**
     * Get the parent node instance.
     *
     * @return Node|null
     */
    public function getParent(): ?Node
    {
        return $this->parent;
    }

    /**
     * Returns a list of the ancestors that is ordered starting from the root by the depth. Index of an element in that
     * list corresponds its depth (if depth of the root is 0).
     *
     * @return Node[]
     */
    public function getParentTree(): iterable
    {
        $ancestors = [];

        for ($ancestor = $this->getParent(); !is_null($ancestor); $ancestor = $ancestor->getParent()) {
            $ancestors[] = $ancestor;
        }

        return array_reverse($ancestors, true);
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
     * @return Node|null
     */
    public function getRoot(): ?TagNode
    {
        return $this->root;
    }

    /**
     * @return Node[]
     */
    abstract public function getMinimalDeletedSet(int $id): iterable;

    /**
     * @return void
     */
    public function detectIgnorableWhiteSpace(): void
    {
        // no op.
    }

    /**
     * Descent the ancestors list for both nodes stopping either at the first no-match case or when either of the lists
     * is exhausted.
     *
     * @param  Node $other
     * @return LastCommonParentResult
     */
    public function getLastCommonParent(Node $other): LastCommonParentResult
    {
        $result = new LastCommonParentResult();

        // These lists can be empty.
        $myParents    = $this->getParentTree();
        $otherParents = $other->getParentTree();

        $i = 1;
        $isSame = true;

        while ($isSame && $i < count($myParents) && $i < count($otherParents)) {
            if ($myParents[$i] && $otherParents[$i] && !$myParents[$i]->isSameTag($otherParents[$i])) {
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
        }
        elseif (count($myParents) < count($otherParents)) {
            // Current node is not so deeply nested.
            $result->setIndexInLastCommonParent($myParents[$i - 1]->getIndexOf($this));
        }
        elseif (count($myParents) > count($otherParents)) {
            // All tags matched but there are tags left in this tree - $other node is not so deeply nested.
            $result->setIndexInLastCommonParent($myParents[$i - 1].getIndexOf($myParents[$i]))
            $result->setSplittingNeeded();
        }
        else {
            // All tags matched until the very last one in both trees or there were not tags besides the BODY.
            $result->setIndexInLastCommonParent($myParents[$i - 1]->getIndexOf($this));
        }

        return $result;
    }

    /**
     * Changes the $parent field of this node. Does NOT append/remvoe iteself from the previous or the new parent
     * children collection.
     *
     * @param  TagNode|null $parent
     * @return self
     */
    public function setParent(?TagNode $parent): self
    {
        $this->parent = $parent;

        if (!is_null($parent)) {
            $this->setRoot($parent->getRoot());
        }

        return $this;
    }

    /**
     * @param  TagNode $root
     * @return self
     */
    protected function setRoot(TagNode $root): self
    {
        $this->root = $root;

        return $this;
    }

    /**
     * Return a deep copy of this node tree.
     *
     * @return Node
     */
    abstract public function copyTree(): Node;

    /**
     * Return true only if one of the ancestors is a <pre> tag.  False otherwise, including the case where this node is
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
     * @param  bool $value
     * @return self
     */
    public function setWhiteBefore(bool $value): self
    {
        $this->whiteBefore = $value;

        return $this;
    }

    /**
     * @return bool
     */
    public function isWhiteAfter(): bool
    {
        return $this->whiteAfter;
    }

    /**
     * @param  bool $value
     * @return self
     */
    public function setWhiteAfter(bool $value): self
    {
        $this->whiteAfter = $value;

        return $this;
    }

    /**
     * @return Node
     */
    abstract public function getLeftMostChild(): Node;

    /**
     * @return Node
     */
    abstract public function getRightMostChild(): Node;
}
