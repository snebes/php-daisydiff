<?php
/**
 * (c) Steve Nebes <snebes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace SN\DaisyDiff\Contracts;

/**
 * A visitor visits supported DOM nodes.
 */
interface NodeVisitorInterface
{
    /**
     * Whether this visitor supports the DOMNode or not in the current context.
     *
     * @param \DOMNode $domNode
     * @param          $cursor
     * @return bool
     */
    public function supports(\DOMNode $domNode, $cursor): bool;

    /**
     * Enter the DOMNode.
     *
     * @param \DOMNode $domNode
     * @param          $cursor
     * @return mixed
     */
    public function enterNode(\DOMNode $domNode, $cursor);

    /**
     * Leave the DOMNode.
     *
     * @param \DOMNode $domNode
     * @param          $cursor
     * @return mixed
     */
    public function leaveNode(\DOMNode $domNode, $cursor);
}
