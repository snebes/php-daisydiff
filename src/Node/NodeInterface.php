<?php
/**
 * (c) Steve Nebes <snebes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace SN\DaisyDiff\Node;

/**
 * Represents a node.
 */
interface NodeInterface
{
    /**
     * Returns this nodes' parent node, if it has one.
     *
     * @return NodeInterface|null
     */
    public function getParent(): ?NodeInterface;

    /**
     * Add a child to this node.
     *
     * @param NodeInterface $node
     */
    public function addChild(NodeInterface $node): void;

    /**
     * Render this node as a string.
     *
     * @return string
     */
    public function render(): string;
}
