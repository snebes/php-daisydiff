<?php
/**
 * (c) Steve Nebes <snebes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SN\DaisyDiff\Node;

/**
 * Common methods for nodes with children.
 */
trait HasChildrenTrait
{
    /** @var NodeInterface[] */
    private $children = [];

    /**
     * @param NodeInterface $node
     */
    public function addChild(NodeInterface $node): void
    {
        $this->children[] = $node;
    }

    /**
     * @return string
     */
    public function renderChildren(): string
    {
        $rendered = '';

        foreach ($this->children as $child) {
            $rendered .= $child->render();
        }

        return $rendered;
    }
}
