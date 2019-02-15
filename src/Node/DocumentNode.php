<?php
/**
 * (c) Steve Nebes <snebes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SN\DaisyDiff\Node;

/**
 * Root node of the HTML. Contains all other nodes.
 */
class DocumentNode implements NodeInterface
{
    use HasChildrenTrait;

    /**
     * @return NodeInterface|null
     */
    public function getParent(): ?NodeInterface
    {
        return null;
    }

    /**
     * @return string
     */
    public function render(): string
    {
        return $this->renderChildren();
    }
}
