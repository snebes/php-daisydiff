<?php declare(strict_types=1);

namespace DaisyDiff\Html\Dom;

use InvalidArgumentException;
use OutOfBoundsException;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use TypeError;

/**
 * TagNode Tests.
 *
 * @covers DaisyDiff\Html\Dom\TagNode::__construct
 */
class TagNodeTest extends TestCase
{
    /**
     * @covers DaisyDiff\Html\Dom\TagNode::addChild
     */
    public function testAddChild(): void
    {
        $root = new TagNode(null, 'root');
        $intermediate = new TagNode($root, 'middle');
        $root->addChild($intermediate);

        $this->assertEquals($intermediate, $root->getChild(0));
    }

    /**
     * @covers DaisyDiff\Html\Dom\TagNode::addChild
     * @expectedException TypeError
     */
    public function testAddChildNullExcpetion(): void
    {
        $root = new TagNode(null, 'root');
        $root->addChild(null);
    }

    /**
     * @covers DaisyDiff\Html\Dom\TagNode::addChild
     * @expectedException InvalidArgumentException
     */
    public function testAddChildExcpetion(): void
    {
        $root = new TagNode(null, 'root');
        $errorRoot = new TagNode(null, 'errorRoot');
        $intermediate = new TagNode($errorRoot, 'middle');

        try {
            $root->addChild($intermediate);
        } catch (InvalidArgumentException $e) {
            $this->assertEquals('The new child must have this node as a parent.', $e->getMessage());
            throw $e;
        }
    }

    /**
     * @covers DaisyDiff\Html\Dom\TagNode::addChild
     */
    public function testAddChildIndex(): void
    {
        $root = new TagNode(null, 'root');
        $intermediate = new TagNode($root, 'middle');
        $leaf = new TagNode($root, 'leaf');

        $root->addChild($intermediate);
        $root->addChild($leaf, 1);

        $this->assertEquals($leaf, $root->getChild(1));
    }

    /**
     * @covers DaisyDiff\Html\Dom\TagNode::addChild
     * @expectedException TypeError
     */
    public function testAddChildIndexNullException(): void
    {
        $root = new TagNode(null, 'root');

        try {
            $root->addChild(null, 4);
        } catch (TypeError $e) {
            $this->assertEquals(0, $e->getCode());
            throw $e;
        }
    }


    /**
     * @covers DaisyDiff\Html\Dom\TagNode::addChild
     * @expectedException InvalidArgumentException
     */
    public function testAddChildIndexException(): void
    {
        $root = new TagNode(null, 'root');
        $errorRoot = new TagNode(null, 'errorRoot');
        $intermediate = new TagNode($errorRoot, 'middle');

        try {
            $root->addChild($intermediate, 0);
        } catch (InvalidArgumentException $e) {
            $this->assertEquals('The new child must have this node as a parent.', $e->getMessage());
            throw $e;
        }
    }

    /**
     * @covers DaisyDiff\Html\Dom\TagNode::setRoot
     */
    public function testSetRoot(): void
    {
        $root = new TagNode(null, 'root');
        $intermediate = new TagNode($root, 'middle');
        $leaf = new TagNode($intermediate, 'leaf');

        $refMethod = new ReflectionMethod(TagNode::class, 'setRoot');
        $refMethod->setAccessible(true);

        $intermediate->addChild($leaf);
        $refMethod->invoke($intermediate, $root);

        $this->assertEquals($root, $intermediate->getRoot());
    }

    /**
     * @covers DaisyDiff\Html\Dom\TagNode::getIndexOf
     */
    public function testGetIndexOf(): void
    {
        $root = new TagNode(null, 'root');
        $intermediate = new TagNode($root, 'middle');
        $root->addChild($intermediate);

        $leaf1 = new TagNode($intermediate, 'leaf1');
        $intermediate->addChild($leaf1);
        $leaf2 = new TagNode($intermediate, 'leaf2');
        $intermediate->addChild($leaf2);

        $this->assertEquals(-1, $root->getIndexOf($leaf1));
        $this->assertEquals(2, $intermediate->getIndexOf($leaf2));
    }

    /**
     * @covers DaisyDiff\Html\Dom\TagNode::getChild
     */
    public function testGetChild(): void
    {
        $root = new TagNode(null, 'root');
        $intermediate = new TagNode($root, 'middle');
        $root->addChild($intermediate);

        $leaf1 = new TagNode($intermediate, 'leaf1');
        $intermediate->addChild($leaf1);
        $leaf2 = new TagNode($intermediate, 'leaf2');
        $intermediate->addChild($leaf2);

        $this->assertEquals($leaf1, $intermediate->getChild(1));
        $this->assertEquals($leaf2, $intermediate->getChild(2));
    }

    /**
     * @covers DaisyDiff\Html\Dom\TagNode::getChild
     * @expectedException OutOfBoundsException
     */
    public function testGetChildException(): void
    {
        $root = new TagNode(null, 'root');
        $intermediate = new TagNode($root, 'middle');
        $root->addChild($intermediate);

        try {
            $root->getChild(5);
        } catch (OutOfBoundsException $e) {
            $this->assertEquals('Index: 5, Size: 2', $e->getMessage());
            throw $e;
        }
    }

    /**
     * @covers DaisyDiff\Html\Dom\TagNode::getNumChildren
     */
    public function testGetNumChildren(): void
    {
        $root = new TagNode(null, 'root');
        $intermediate = new TagNode($root, 'middle');
        $root->addChild($intermediate);

        $leaf = new TagNode($root, 'leaf');
        $root->addChild($leaf);

        $this->assertEquals(4, $root->getNumChildren());
        $this->assertEquals(0, $leaf->getNumChildren());
    }

    /**
     * @covers DaisyDiff\Html\Dom\TagNode::getIterator
     */
    public function testGetIterator(): void
    {
        $root = new TagNode(null, 'root');
        $intermediate = new TagNode($root, 'middle');
        $root->addChild($intermediate);

        $leaf = new TagNode($root, 'leaf');
        $root->addChild($leaf);

        $iterator = $root->getIterator();
        $iterator->next();
        $this->assertEquals($intermediate, $iterator->current());
    }

    /**
     * @covers DaisyDiff\Html\Dom\TagNode::getQName
     */
    public function testGetQName(): void
    {
        $root = new TagNode(null, 'root');

        $this->assertEquals('root', $root->getQName());
    }

    /**
     * @covers DaisyDiff\Html\Dom\TagNode::isSameTag
     */
    public function testIsSameTag(): void
    {
        $root = new TagNode(null, 'root');
        $compareRoot = new TagNode(null, 'root');
        $intermediate = new TagNode($root, 'middle');
        $compareIntermediate = new TagNode($compareRoot, 'middle');

        $root->addChild($intermediate);
        $compareRoot->addChild($compareIntermediate);

        $this->assertTrue($root->isSameTag($root));
        $this->assertFalse($root->isSameTag(null));
        $this->assertTrue($root->isSameTag($compareRoot));
        $this->assertFalse($root->isSameTag($compareIntermediate));
        $this->assertTrue($intermediate->isSameTag($compareIntermediate));
    }

    /**
     * @covers DaisyDiff\Html\Dom\TagNode::isSimilarTag
     */
    public function testIsSimilarTag(): void
    {
        $root1 = new TagNode(null, 'root');
        $root2 = new TagNode(null, 'root');
        $intermediate = new TagNode(null, 'middle');
        $root = new TagNode(null, 'root');
        $textNode = new TextNode($root, 'Content of the root node');
        $whiteSpaceNode = new WhiteSpaceNode($root, 'root', $textNode);

        $refMethod = new ReflectionMethod(TagNode::class, 'isSimilarTag');
        $refMethod->setAccessible(true);

        $this->assertFalse($refMethod->invoke($root2, $whiteSpaceNode));
        $this->assertTrue($refMethod->invoke($root1, $root2));
        $this->assertFalse($refMethod->invoke($intermediate, $root2));
    }

    /**
     * @covers DaisyDiff\Html\Dom\TagNode::getOpeningTag
     * @covers DaisyDiff\Html\Dom\TagNode::__toString
     */
    public function testGetOpeningTag(): void
    {
        $html  = '<table width="500" height="175">';
        $attrs = [
            'width'  => 500,
            'height' => 175,
        ];

        $root = new TagNode(null, 'table', $attrs);
        $intermediate = new TagNode(null, 'middle');

        $this->assertEquals('<middle>', $intermediate->getOpeningTag());
        $this->assertEquals($html, $root->getOpeningTag());
        $this->assertEquals($html, strval($root));
    }

    /**
     * @covers DaisyDiff\Html\Dom\TagNode::getClosingTag
     */
    public function testGetClosingTag(): void
    {
        $root = new TagNode(null, 'root');
        $intermediate = new TagNode($root, 'middle');
        $root->addChild($intermediate);

        $this->assertEquals('</middle>', $intermediate->getClosingTag());
    }

    /**
     * @covers DaisyDiff\Html\Dom\TagNode::isBlockLevel
     * @covers DaisyDiff\Html\Dom\TagNode::isBlockLevelStatic
     */
    public function testIsBlockLevel(): void
    {
        $root = new TagNode(null, 'html');
        $intermediate = new TagNode($root, 'middle');
        $root->addChild($intermediate);

        $this->assertFalse($intermediate->isBlockLevel());
        $this->assertTrue($root->isBlockLevel());

        $this->assertFalse(TagNode::isBlockLevelStatic($intermediate));
        $this->assertTrue(TagNode::isBlockLevelStatic($root->getQName()));
    }

    /**
     * @covers DaisyDiff\Html\Dom\TagNode::isBlockLevelStatic
     * @expectedException InvalidArgumentException
     */
    public function testIsBlockLevelException(): void
    {
        try {
            $this->assertFalse(TagNode::isBlockLevelStatic(null));
        } catch (InvalidArgumentException $e) {
            $this->assertEquals('Only string or Node values allowed.', $e->getMessage());
            throw $e;
        }
    }

    /**
     * @covers DaisyDiff\Html\Dom\TagNode::isInline
     * @covers DaisyDiff\Html\Dom\TagNode::isInlineStatic
     */
    public function testIsInline(): void
    {
        $root = new TagNode(null, 'ul');
        $intermediate = new TagNode($root, 'middle');
        $root->addChild($intermediate);

        $this->assertTrue($intermediate->isInline());
        $this->assertFalse($root->isInline());

        $this->assertFalse(TagNode::isInlineStatic($root));
        $this->assertTrue(TagNode::isInlineStatic($intermediate->getQName()));
    }

    /**
     * @covers DaisyDiff\Html\Dom\TagNode::copyTree
     */
    public function testCopyTree(): void
    {
        $root = new TagNode(null, 'root');
        $intermediate = new TagNode($root, 'middle');
        $root->addChild($intermediate);

        $leaf1 = new TagNode($intermediate, 'leaf1');
        $intermediate->addChild($leaf1);
        $leaf2 = new TagNode($intermediate, 'leaf2');
        $intermediate->addChild($leaf2);

        $this->assertEquals($root, $root->copyTree());
    }

    /**
     * @covers DaisyDiff\Html\Dom\TagNode::getMatchRatio
     */
/*
    public function testGetMatchRatio(): void
    {
        $root = new TagNode(null, 'root');
        $textNode = new TextNode($root, 'Content of the root node');
        $intermediate = new TagNode($root, 'root');
        $text = new TextNode($intermediate, 'Content of the intermdeiate node');

        $this->assertEquals(0.25, $root->getMatchRatio($intermediate), '', 0.1);
    }
*/

    /**
     * @covers DaisyDiff\Html\Dom\TagNode::getLeftMostChild
     * @covers DaisyDiff\Html\Dom\TagNode::getRightMostChild
     */
    public function testGetLeftRightMostChild(): void
    {
        $root = new TagNode(null, 'root');
        $intermediate = new TagNode($root, 'middle');
        $root->addChild($intermediate);

        $leaf1 = new TagNode($intermediate, 'leaf1');
        $intermediate->addChild($leaf1);
        $leaf2 = new TagNode($intermediate, 'leaf2');
        $intermediate->addChild($leaf2);

        $this->assertEquals($leaf1, $root->getLeftMostChild());
        $this->assertEquals($leaf2, $root->getRightMostChild());
    }

    /**
     * @covers DaisyDiff\Html\Dom\TagNode::isPre
     */
    public function testIsPre(): void
    {
        $root = new TagNode(null, 'pre');
        $intermediate = new TagNode($root, 'middle');

        $this->assertTrue($root->isPre());
        $this->assertFalse($intermediate->isPre());
    }


}
