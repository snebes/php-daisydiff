<?php
/**
 * (c) Steve Nebes <snebes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace SN\DaisyDiff\Visitor;

use SN\DaisyDiff\Node\Cursor;

/**
 * A visitor visits supported DOM nodes.
 */
interface NodeVisitorInterface
{
    /**
     * Whether this visitor supports the DOMNode or not in the current context.
     *
     * @param \DOMNode $domNode
     * @param Cursor   $cursor
     * @return bool
     */
    public function supports(\DOMNode $domNode, Cursor $cursor): bool;

    /**
     * Enter the DOMNode.
     *
     * @param \DOMNode $domNode
     * @param Cursor   $cursor
     * @return mixed
     */
    public function enterNode(\DOMNode $domNode, Cursor $cursor);

    /**
     * Leave the DOMNode.
     *
     * @param \DOMNode $domNode
     * @param Cursor   $cursor
     * @return mixed
     */
    public function leaveNode(\DOMNode $domNode, Cursor $cursor);
}
