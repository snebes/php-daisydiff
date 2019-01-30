<?php
/**
 * (c) Steve Nebes <snebes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace SN\DaisyDiff\Html\Dom;

use ArrayIterator;
use SN\DaisyDiff\Html\Ancestor\TextOnlyComparator;
use IteratorAggregate;

/**
 * Node that can contain other nodes. Represents an HTML tag.
 */
class TagNode extends Node implements IteratorAggregate
{
    /** @var Node[] */
    private $children = [];

    /** @var string */
    private $qName;

    /** @var array */
    private $attributes = [];

    /**
     * @param TagNode|null $parent
     * @param string       $qName
     * @param array        $attributes
     */
    public function __construct(?TagNode $parent, string $qName, array $attributes = [])
    {
        parent::__construct($parent);
        $this->qName = \mb_strtolower($qName);
        $this->attributes = $attributes;
    }

    /**
     * Appends the provided node ot the collection of children if $this node is set as teh parameter's parent. This
     * method is used in Node's constructor.
     *
     * @param Node $node
     * @param int  $index
     *
     * @throws \InvalidArgumentException
     */
    public function addChild(Node $node, int $index = null): void
    {
        if ($node->getParent() !== $this) {
            throw new \InvalidArgumentException('The new child must have this node as a parent.');
        }

        if (null !== $index) {
            \array_splice($this->children, $index, 0, [$node]);
        } else {
            $this->children[] = $node;
        }
    }

    /**
     * Update the root nodes of all child nodes.
     *
     * @param TagNode $root
     */
    protected function setRoot(TagNode $root): void
    {
        parent::setRoot($root);

        foreach ($this->getIterator() as $child) {
            $child->setRoot($root);
        }
    }

    /**
     * If the provided parameter is in the same tree with $this object then this method fetches index of the parameter
     * object in the children collection. If the parameter is from a different tree, then this method attempts to return
     * the index of first semantically equivalent node to the parameter.
     *
     * @param Node $child Tag we need an index for.
     * @return int Index of first semantically equivalent child or -1 if couldn't find one.
     */
    public function getIndexOf(Node $child): int
    {
        $key = \array_search($child, $this->children, true);

        if (false !== $key && \is_int($key)) {
            return $key;
        }

        return -1;
    }

    /**
     * @param int $index
     * @return Node
     *
     * @throws \OutOfBoundsException
     */
    public function getChild(int $index): Node
    {
        if (isset($this->children[$index])) {
            return $this->children[$index];
        }

        throw new \OutOfBoundsException(\sprintf('Index: %d, Size: %d', $index, \count($this->children)));
    }

    /**
     * IteratorAggregate::getIterator()
     *
     * @return ArrayIterator
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->children);
    }

    /**
     * @return int
     */
    public function getNumChildren(): int
    {
        return \count($this->children);
    }

    /**
     * @return string
     */
    public function getQName(): string
    {
        return $this->qName;
    }

    /**
     * @return array
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * Checks tags for being semantically equivalent if it's from a different tree and for being the same object if it's
     * from the same tree as $this tag.
     *
     * @param TagNode|null $other
     * @return bool
     */
    public function isSameTag(?TagNode $other): bool
    {
        if (null === $other) {
            return false;
        }

        return $this->equals($other);
    }

    /**
     * Considers tags from different trees equal if they have same name and equivalent attributes. No attention paid to
     * the content (children) of the tag. Considers tags from the same tree equal if it is the same object.
     *
     * @param TagNode $tagNode
     * @return bool
     */
    public function equals(TagNode $tagNode): bool
    {
        if ($tagNode === $this) {
            return true;
        }

        if ($this->getRoot() === $tagNode->getRoot()) {
            // Not the same and in the same tree, so not equal.
            return false;
        }

        // Still a chance for being equal if we are in the different tree, we should use semantic equivalence instead.
        if ($this->isSimilarTag($tagNode)) {
            return true;
        }

        return false;
    }

    /**
     * @param array $otherAttributes
     * @return bool
     */
    private function hasSameAttributes(array $otherAttributes): bool
    {
        // http://php.net/manual/en/language.operators.array.php
        // $a == $b is TRUE if $a and $b have the same key/value pairs, order does not matter.
        return $this->getAttributes() == $otherAttributes;
    }

    /**
     * Returns true if this tag is similar to the given other tag. The tags may be from different trees. If the tag name
     * and attributes are the same, the result will be true.
     *
     * @param Node $other
     * @return bool
     */
    protected function isSimilarTag(Node $other): bool
    {
        $result = false;

        if ($other instanceof TagNode) {
            if ($this->getQName() === $other->getQName()) {
                $result = $this->hasSameAttributes($other->getAttributes());
            }
        }

        return $result;
    }

    /**
     * Produces string for the opening HTML tag for this node. Includes the attributes. This probably doesn't work for
     * image tag.
     *
     * @return string
     */
    public function getOpeningTag(): string
    {
        $s = '<' . $this->getQName();

        foreach ($this->getAttributes() as $name => $value) {
            $s .= \sprintf(' %s="%s"', $name, $value);
        }

        $s .= '>';

        return $s;
    }

    /**
     * Return the closing HTML tag that corresponds to the current node. Probably doesn't work for image tag.
     *
     * @return string
     */
    public function getEndTag(): string
    {
        return \sprintf('</%s>', $this->getQName());
    }

    /**
     * This recursive method considers a descendant deleted if all its children had TextNodes that now are marked as
     * removed with the provided id. If all children of a descendant is considered deleted, only that descendant is kept
     * in the collection of the deleted nodes, and its children are removed from the collection of the deleted nodes.
     *
     * The HTML tag nodes that never had any text content are never considered removed.
     *
     * It actually might have nothing to do with being really deleted, because the element might be kept after its text
     * content was deleted.
     *
     * Example:
     *      table cells can be kept after its text content was deleted
     *      horizontal rule has never had text content, but can be deleted
     *
     * @param int $id
     * @return Node[]
     */
    public function getMinimalDeletedSet(int $id): array
    {
        $nodes = [];

        // No-content tags are never included in the set.
        if (0 === \count($this->children)) {
            return $nodes;
        }

        // By default, we think that all children are in the deleted set until we prove otherwise.
        $hasNotDeletedDescendant = false;

        foreach ($this->getIterator() as $child) {
            $childrenChildren = $child->getMinimalDeletedSet($id);
            $nodes = \array_merge($nodes, $childrenChildren);

            if (!$hasNotDeletedDescendant &&
                !(1 === \count($childrenChildren) && \in_array($child, $childrenChildren, true))) {
                // This child is not entirely deleted.
                $hasNotDeletedDescendant = true;
            }
        }

        // If all children are in the deleted set, remove them and put $this instead.
        if (!$hasNotDeletedDescendant) {
            $nodes = [$this];
        }

        return $nodes;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->getOpeningTag();
    }

    /**
     * Attempts to create 2 TagNodes with the same name and attributes as the original $this node. All children
     * preceding split parameter are placed into the left part, all children following the split parameter are placed
     * into the right part. Placement of the split node is determined by $includeLeft flag parameter. The newly created
     * nodes are only added to the parent of $this node if they have some children. The original $this node is removed
     * afterwards. The process proceeds recursively hiking up the tree until the "parent" node is reached. "Parent" node
     * will not be touched. This method is used when the parent tags of a deleted TextNode can no longer be found in the
     * new doc. (means they either has been deleted or changed arguments). The "parent" parameter in that case is the
     * deepest common parent between the deleted node and its surrounding remaining siblings.
     *
     * @param TagNode $parent
     * @param Node    $split
     * @param bool    $includeLeft
     * @return bool
     */
    public function splitUntil(TagNode $parent, Node $split, bool $includeLeft): bool
    {
        $splitOccurred = false;

        if ($parent !== $this) {
            $part1 = new TagNode(null, $this->getQName(), $this->getAttributes());
            $part2 = new TagNode(null, $this->getQName(), $this->getAttributes());
            $part1->setParent($this->getParent());
            $part2->setParent($this->getParent());

            $i = 0;
            $iMax = \count($this->children);

            while ($i < $iMax && $this->children[$i] !== $split) {
                $this->children[$i]->setParent($part1);
                $part1->addChild($this->children[$i]);
                $i++;
            }

            if ($i < $iMax) {
                // We've found a split node.
                if ($includeLeft) {
                    $this->children[$i]->setParent($part1);
                    $part1->addChild($this->children[$i]);
                } else {
                    $this->children[$i]->setParent($part2);
                    $part2->addChild($this->children[$i]);
                }

                $i++;
            }

            while ($i < $iMax) {
                $this->children[$i]->setParent($part2);
                $part2->addChild($this->children[$i]);
                $i++;
            }

            if ($part1->getNumChildren() > 0) {
                $this->getParent()->addChild($part1, $this->getParent()->getIndexOf($this));
            }

            if ($part2->getNumChildren() > 0) {
                $this->getParent()->addChild($part2, $this->getParent()->getIndexOf($this));
            }

            if ($part1->getNumChildren() > 0 && $part2->getNumChildren() > 0) {
                $splitOccurred = true;
            }

            // Since split isn't meant for no-children tags, we won't have a case where we removed $this and did not
            // substitute it with anything.
            $this->getParent()->removeChild($this);

            if ($includeLeft) {
                $this->getParent()->splitUntil($parent, $part1, $includeLeft);
            } else {
                $this->getParent()->splitUntil($parent, $part2, $includeLeft);
            }
        }

        return $splitOccurred;
    }

    /**
     * @param Node $node
     */
    public function removeChild(Node $node): void
    {
        $key = \array_search($node, $this->children, true);

        if (false !== $key && \is_int($key)) {
            \array_splice($this->children, $key, 1);
        }
    }

    /** @var string[] */
    private static $blocks = [
        'html', 'body', 'p', 'blockquote', 'h1', 'h2', 'h3', 'h4', 'h5', 'pre', 'div', 'ul', 'ol', 'li',
        'table', 'tbody', 'tr', 'td', 'th', 'br', 'thead', 'tfoot',
    ];

    /**
     * @return bool
     */
    public function isBlockLevel(): bool
    {
        return \in_array($this->getQName(), self::$blocks, true);
    }

    /**
     * @return bool
     */
    public function isInline(): bool
    {
        return !$this->isBlockLevel();
    }

    /**
     * {@inheritdoc}
     */
    public function copyTree(): Node
    {
        $newThis = new TagNode(null, $this->getQName(), $this->getAttributes());
        $newThis->setWhiteBefore($this->isWhiteBefore());
        $newThis->setWhiteAfter($this->isWhiteAfter());

        foreach ($this->getIterator() as $child) {
            $newChild = $child->copyTree();
            $newChild->setParent($newThis);
            $newThis->addChild($newChild);
        }

        return $newThis;
    }

    /**
     * @param TagNode $other
     * @return float
     */
    public function getMatchRatio(TagNode $other): float
    {
        $thisComp = new TextOnlyComparator($this);
        $otherComp = new TextOnlyComparator($other);

        return $otherComp->getMatchRatio($thisComp);
    }

    /**
     * @return void
     */
    public function expandWhiteSpace(): void
    {
        $shift = 0;
        $spaceAdded = false;

        for ($i = 0, $iMax = $this->getNumChildren(); $i < $iMax; $i++) {
            $child = $this->getChild($i + $shift);

            if ($child instanceof TagNode && !$child->isPre()) {
                $child->expandWhiteSpace();
            }

            if (!$spaceAdded && $child->isWhiteBefore()) {
                $ws = new WhiteSpaceNode(null, ' ', $child->getLeftMostChild());
                $ws->setParent($this);
                $this->addChild($ws, $i + ($shift++));
            }

            if ($child->isWhiteAfter()) {
                $ws = new WhiteSpaceNode(null, ' ', $child->getRightMostChild());
                $ws->setParent($this);
                $this->addChild($ws, $i + 1 + ($shift++));
                $spaceAdded = true;
            } else {
                $spaceAdded = false;
            }
        }
    }

    /**
     * @return Node
     */
    public function getLeftMostChild(): Node
    {
        if ($this->getNumChildren() < 1) {
            return $this;
        }

        $child = $this->getChild(0);

        return $child->getLeftMostChild();
    }

    /**
     * @return Node
     */
    public function getRightMostChild(): Node
    {
        if ($this->getNumChildren() < 1) {
            return $this;
        }

        $child = $this->getChild($this->getNumChildren() - 1);

        return $child->getRightMostChild();
    }

    /**
     * @return bool
     */
    public function isPre(): bool
    {
        return 'pre' === $this->getQName();
    }
}
