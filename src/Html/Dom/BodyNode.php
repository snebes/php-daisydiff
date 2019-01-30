<?php
/**
 * (c) Steve Nebes <snebes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace DaisyDiff\Html\Dom;

/**
 * Represents the root of a HTML document.
 */
class BodyNode extends TagNode
{
    /**
     * Default values.
     */
    public function __construct()
    {
        parent::__construct(null, 'body');
    }

    /**
     * @return Node
     */
    public function copyTree(): Node
    {
        $newThis = new static();

        /** @var Node $child */
        foreach ($this->getIterator() as $child) {
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

        foreach ($this->getIterator() as $child) {
            $childrenChildren = $child->getMinimalDeletedSet($id);
            $nodes = \array_merge($nodes, $childrenChildren);
        }

        return $nodes;
    }
}
