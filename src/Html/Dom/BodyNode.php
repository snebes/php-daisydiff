<?php declare(strict_types=1);

namespace DaisyDiff\Html\Dom;

/**
 * Represents the root of a HTML document.
 */
class BodyNode extends TagNode
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct(null, 'body');
    }

    /**
     * {@inheritdoc}
     */
    public function copyTree(): Node
    {
        $newThis = new static();

        foreach ($this as $child) {
            $newChild = $child->copyTree();
            $newChild->setParent($newThis);
            $newThis->addChild($newChild);
        }

        return $newThis;
    }

    /**
     * {@inheritdoc}
     */
    public function getMinimalDeletedSet(int $id): iterable
    {
        $nodes = [];

        foreach ($this as $child) {
            $childrenChildren = $child->getMinimalDeletedSet($id);
            $nodes = array_merge($nodes, $childrenChildren);
        }

        return $nodes;
    }
}
