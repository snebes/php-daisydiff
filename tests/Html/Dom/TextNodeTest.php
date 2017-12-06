<?php declare(strict_types=1);

namespace DaisyDiff\Html\Dom;

use DaisyDiff\Html\Modification\Modification;
use DaisyDiff\Html\Modification\ModificationType;
use PHPUnit\Framework\TestCase;

/**
 * TextNode Tests.
 *
 * @covers DaisyDiff\Html\Dom\TextNode::__construct
 */
class TextNodeTest extends TestCase
{
    /**
     * @covers DaisyDiff\Html\Dom\TextNode::copyTree
     */
    public function testCopyTree(): void
    {
        $root = new TagNode(null, 'root');
        $textRoot = new TextNode($root, 'contents of root node');
        $copyRoot = $textRoot->copyTree();

        $this->assertEquals($textRoot->getText(), $copyRoot->getText());
    }

    /**
     * @covers DaisyDiff\Html\Dom\TextNode::getLeftMostChild
     * @covers DaisyDiff\Html\Dom\TextNode::getRightMostChild
     */
    public function testGetLeftRightMostChild(): void
    {
        $root = new TagNode(null, 'root');
        $textRoot = new TextNode($root, 'root');

        $this->assertEquals($textRoot, $textRoot->getLeftMostChild());
        $this->assertEquals($textRoot, $textRoot->getRightMostChild());
    }

    /**
     * @covers DaisyDiff\Html\Dom\TextNode::getModification
     * @covers DaisyDiff\Html\Dom\TextNode::setModification
     */
    public function testGetModificationText(): void
    {
        $root = new TagNode(null, 'root');
        $textRoot = new TextNode($root, 'root');
        $textRoot->setModification(null);

        $this->assertNull($textRoot->getModification());
    }

    /**
     * @covers DaisyDiff\Html\Dom\TextNode::getText
     * @covers DaisyDiff\Html\Dom\TextNode::__toString
     */
    public function testGetText(): void
    {
        $root = new TagNode(null, 'root');
        $textRoot = new TextNode($root, 'root');

        $this->assertEquals('root', $textRoot->getText());
        $this->assertEquals('root', strval($textRoot));
    }

    /**
     * @covers DaisyDiff\Html\Dom\TextNode::isSameText
     */
    public function testIsSameText(): void
    {
        $root = new TagNode(null, 'root');
        $textRoot = new TextNode($root, 'root');
        $textBody = new TextNode($root, 'root');

        $this->assertTrue($textRoot->isSameText($textBody));
        $this->assertFalse($textRoot->isSameText(null));
        $this->assertFalse($textRoot->isSameText($root));
    }

    /**
     * @covers DaisyDiff\Html\Dom\TextNode::getMinimalDeletedSet
     */
    public function testGetMinimalDeletedSet(): void
    {
        $root = new TagNode(null, 'root');
        $textRoot = new TextNode($root, 'contents of root node');
        $intermediate = new TagNode($root, 'intermediate');
        $textIntermediate = new TextNode($root, 'contents of intermediate node');

        $this->assertEquals([], $textRoot->getMinimalDeletedSet(0));

        $mod = new Modification(ModificationType::REMOVED, ModificationType::REMOVED);
        $mod->setId(0);
        $textIntermediate->setModification($mod);

        $this->assertEquals([$textIntermediate], $textIntermediate->getMinimalDeletedSet(0));
    }
}
