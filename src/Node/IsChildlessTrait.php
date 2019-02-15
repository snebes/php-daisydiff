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
 * Used by nodes which don't have children.
 */
trait IsChildlessTrait
{
    /**
     * @param NodeInterface $node
     */
    public function addChild(NodeInterface $node): void
    {
    }
}
