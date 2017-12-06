<?php declare(strict_types=1);

namespace DaisyDiff\Html\Dom;

use PHPUnit\Framework\TestCase;

/**
 * WhiteSpaceNode Tests.
 *
 * @covers DaisyDiff\Html\Dom\WhiteSpaceNode::__construct
 */
class WhiteSpaceNodeTest extends TestCase
{
    /**
     * @covers DaisyDiff\Html\Dom\WhiteSpaceNode::__construct
     */
    public function testCopyTree(): void
    {
        $root = new TagNode(null, 'root');
        $textNode = new TextNode($root, 'contents of root node');

        $whiteSpaceNode = new WhiteSpaceNode($root, 'root', $textNode);
        $whiteSpaceNodeNullPointer = new WhiteSpaceNode(null, 'root', null);

        $this->assertEquals($root, $whiteSpaceNode->getParent());
        $this->assertNull($whiteSpaceNodeNullPointer->getParent());
    }

    /**
     * @covers DaisyDiff\Html\Dom\WhiteSpaceNode::isWhiteSpace
     */
    public function testIsWhiteSpace(): void
    {
        $this->assertFalse(WhiteSpaceNode::isWhiteSpace('a'));
        $this->assertTrue(WhiteSpaceNode::isWhiteSpace("\n"));
        $this->assertTrue(WhiteSpaceNode::isWhiteSpace(' '));
        $this->assertTrue(WhiteSpaceNode::isWhiteSpace("\t"));
        $this->assertTrue(WhiteSpaceNode::isWhiteSpace("\r"));
    }
}
