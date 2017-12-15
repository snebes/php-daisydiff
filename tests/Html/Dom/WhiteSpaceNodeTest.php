<?php declare(strict_types=1);

namespace DaisyDiff\Html\Dom;

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

        $this->assertEquals($root, $whiteSpaceNode->getParent());
        $this->assertNull($whiteSpaceNodeNullPointer->getParent());
    }

    public function testIsWhiteSpace(): void
    {
        $this->assertFalse(WhiteSpaceNode::isWhiteSpace('a'));
        $this->assertTrue(WhiteSpaceNode::isWhiteSpace("\n"));
        $this->assertTrue(WhiteSpaceNode::isWhiteSpace(' '));
        $this->assertTrue(WhiteSpaceNode::isWhiteSpace("\t"));
        $this->assertTrue(WhiteSpaceNode::isWhiteSpace("\r"));
    }
}
