<?php

declare(strict_types=1);

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
        parent::__construct(null, 'body', []);
    }

    /**
     * @return Node
     */
    public function copyTree(): Node
    {
        $newThis = new static();

        foreach ($this->children as $child) {
            $newChild = $child->copyTree();
            $newChild->setParent($newThis);
            $newThis->addChild($newChild);
        }

        return $newThis;
    }

    /**
     * @param int $id
     * @return Node[]
     */
    public function getMinimalDeletedSet(int $id): array
    {
        $nodes = [];

        foreach ($this->children as $child) {
            $childrenChildren = $child->getMinimalDeletedSet($id);
            $nodes = array_merge($nodes, $childrenChildren);
        }

        return $nodes;
    }
}
