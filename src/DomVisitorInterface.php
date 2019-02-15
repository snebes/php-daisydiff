<?php
/**
 * (c) Steve Nebes <snebes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace SN\DaisyDiff;

use SN\DaisyDiff\Node\DocumentNode;

/**
 * Visit a parsed DOMNode to create the equivalent DocumentNode.
 */
interface DomVisitorInterface
{
    /**
     * @param \DOMNode $node
     * @return DocumentNode
     */
    public function visit(\DOMNode $node): DocumentNode;
}
