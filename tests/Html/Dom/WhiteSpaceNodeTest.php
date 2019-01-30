<?php
/**
 * (c) Steve Nebes <snebes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace SN\DaisyDiff\Html\Dom;

use PHPUnit\Framework\TestCase;

/**
 * WhiteSpaceNode Tests.
 */
class WhiteSpaceNodeTest extends TestCase
{
    public function testWhiteSpaceNode(): void
    {
        $root = new TagNode(null, 'root');
        $textNode = new TextNode($root, 'contents of root node');

        $whiteSpaceNode = new WhiteSpaceNode($root, 'root', $textNode);
        $whiteSpaceNodeNullPointer = new WhiteSpaceNode(null, 'root', null);

        $this->assertSame($root, $whiteSpaceNode->getParent());
        $this->assertNull($whiteSpaceNodeNullPointer->getParent());
        $this->assertTrue($whiteSpaceNode->isSameText($whiteSpaceNodeNullPointer));
    }

    public function testIsWhiteSpace(): void
    {
        $this->assertFalse(WhiteSpaceNode::isWhiteSpace('a'));
        $this->assertTrue(WhiteSpaceNode::isWhiteSpace(' '));
        $this->assertTrue(WhiteSpaceNode::isWhiteSpace("\t"));
        $this->assertTrue(WhiteSpaceNode::isWhiteSpace("\r"));
        $this->assertTrue(WhiteSpaceNode::isWhiteSpace("\n"));
    }
}
