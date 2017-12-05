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
 ** @covers DaisyDiff\Html\Dom\TagNode::__construct
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

        $refMethod = new ReflectionMethod($intermediate, 'setRoot');
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

        $this->assertFalse($root2->isSimilarTag($whiteSpaceNode));
        $this->assertTrue($root1->isSimilarTag($root2));
        $this->assertFalse($intermediate->isSimilarTag($root2));
    }
}
