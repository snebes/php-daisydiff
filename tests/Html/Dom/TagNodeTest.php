<?php declare(strict_types=1);

namespace DaisyDiff\Html\Dom;

use InvalidArgumentException;
use OutOfBoundsException;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use TypeError;

/**
 * TagNode Tests.
 */
class TagNodeTest extends TestCase
{
    public function testAddChild(): void
    {
        $root = new TagNode(null, 'root');
        $intermediate = new TagNode($root, 'middle');
        $root->addChild($intermediate);

        $this->assertEquals($intermediate, $root->getChild(0));
    }

    /**
     * @expectedException TypeError
     */
    public function testAddChildNullExcpetion(): void
    {
        $root = new TagNode(null, 'root');
        $root->addChild(null);
    }

    /**
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

    public function testAddChildIndex(): void
    {
        $root = new TagNode(null, 'root');
        $intermediate = new TagNode($root, 'middle');
        $root->addChild($intermediate, 0);

        $leaf = new TagNode($root, 'leaf');
        $root->addChild($leaf, 1);

        try {
            $root->addChild(null, 4);
        } catch (TypeError $e) {
        }

        $this->assertEquals($leaf, $root->getChild(1));
    }

    /**
     * @expectedException TypeError
     */
    public function testAddChildIndexNullException(): void
    {
        $root = new TagNode(null, 'root');

        try {
            $root->addChild(null, 1);
        } catch (TypeError $e) {
            $this->assertEquals(0, $e->getCode());
            throw $e;
        }
    }


    /**
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

    public function testSetRoot(): void
    {
        $refMethod = new ReflectionMethod(TagNode::class, 'setRoot');
        $refMethod->setAccessible(true);

        $root = new TagNode(null, 'root');
        $intermediate = new TagNode($root, 'middle');
        $leaf = new TagNode($intermediate, 'leaf');

        $intermediate->addChild($leaf);
        $refMethod->invoke($intermediate, $root);

        $this->assertEquals($root, $intermediate->getRoot());
    }

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

    public function testGetQName(): void
    {
        $root = new TagNode(null, 'root');

        $this->assertEquals('root', $root->getQName());
    }

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

    public function testIsSimilarTag(): void
    {
        $refMethod = new ReflectionMethod(TagNode::class, 'isSimilarTag');
        $refMethod->setAccessible(true);

        $root1 = new TagNode(null, 'root');
        $root2 = new TagNode(null, 'root');
        $intermediate = new TagNode(null, 'middle');
        $root = new TagNode(null, 'root');
        $textNode = new TextNode($root, 'Content of the root node');
        $whiteSpaceNode = new WhiteSpaceNode($root, 'root', $textNode);

        $this->assertFalse($refMethod->invoke($root2, $whiteSpaceNode));
        $this->assertTrue($refMethod->invoke($root1, $root2));
        $this->assertFalse($refMethod->invoke($intermediate, $root2));
    }

    public function testGetOpeningTag(): void
    {
        $html  = '<table class="table" width="500">';
        $attrs = [
            'class' => 'table',
            'width' => 500,
        ];

        $root = new TagNode(null, 'table', $attrs);
        $intermediate = new TagNode(null, 'middle');

        $this->assertEquals('<middle>', $intermediate->getOpeningTag());
        $this->assertEquals($html, $root->getOpeningTag());
        $this->assertEquals($html, strval($root));
    }

    public function testGetClosingTag(): void
    {
        $root = new TagNode(null, 'root');
        $intermediate = new TagNode($root, 'middle');
        $root->addChild($intermediate);

        $this->assertEquals('</middle>', $intermediate->getClosingTag());
        $this->assertEquals('</root>', $root->getClosingTag());
    }

    public function testIsBlockLevel(): void
    {
        $root = new TagNode(null, 'html');
        $intermediate = new TagNode($root, 'middle');
        $root->addChild($intermediate);

        $this->assertFalse($intermediate->isBlockLevel());
        $this->assertTrue($root->isBlockLevel());
    }

    public function testIsInline(): void
    {
        $root = new TagNode(null, 'ul');
        $intermediate = new TagNode($root, 'middle');
        $root->addChild($intermediate);

        $this->assertTrue($intermediate->isInline());
        $this->assertFalse($root->isInline());
    }

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

    // TODO: verify this test
    public function testGetMatchRatio(): void
    {
        $root = new TagNode(null, 'root');
//        $textNode = new TextNode($root, 'Content of the root node');
        $intermediate = new TagNode($root, 'root');
//        $text = new TextNode($intermediate, 'Content of the intermdeiate node');

        $this->assertEquals(0.25, $root->getMatchRatio($intermediate), '', 0.1);
    }

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

    public function testIsPre(): void
    {
        $root = new TagNode(null, 'pre');
        $intermediate = new TagNode($root, 'middle');

        $this->assertTrue($root->isPre());
        $this->assertFalse($intermediate->isPre());
    }
}
