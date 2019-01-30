<?php
/**
 * (c) Steve Nebes <snebes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace DaisyDiff\Html\Dom;

use DaisyDiff\Html\Modification\Modification;
use DaisyDiff\Html\Modification\ModificationType;
use PHPUnit\Framework\TestCase;

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

        $this->assertSame($intermediate, $root->getChild(0));
    }

    /**
     * @expectedException \Error
     */
    public function testAddChildNullException(): void
    {
        $root = new TagNode(null, 'root');

        try {
            $root->addChild(null);
        } catch (\Error $e) {
            $this->assertInstanceOf(\TypeError::class, $e);

            throw $e;
        }
    }

    /**
     * @expectedException \Exception
     */
    public function testAddChildException(): void
    {
        $root = new TagNode(null, 'root');
        $errorRoot = new TagNode(null, 'errorRoot');
        $intermediate = new TagNode($errorRoot, 'middle');

        try {
            $root->addChild($intermediate);
        } catch (\Exception $e) {
            $this->assertInstanceOf(\InvalidArgumentException::class, $e);

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

        $this->assertSame($leaf, $root->getChild(1));
    }

    /**
     * @expectedException \Error
     */
    public function testAddChildIndexNullException(): void
    {
        $root = new TagNode(null, 'root');

        try {
            $root->addChild(null, 1);
        } catch (\Error $e) {
            $this->assertInstanceOf(\TypeError::class, $e);

            throw $e;
        }
    }

    /**
     * @expectedException \Exception
     */
    public function testAddChildIndexException(): void
    {
        $root = new TagNode(null, 'root');
        $errorRoot = new TagNode(null, 'errorRoot');
        $intermediate = new TagNode($errorRoot, 'middle');

        try {
            $root->addChild($intermediate, 0);
        } catch (\Exception $e) {
            $this->assertInstanceOf(\InvalidArgumentException::class, $e);

            throw $e;
        }
    }

    public function testSetRoot(): void
    {
        $refMethod = new \ReflectionMethod(TagNode::class, 'setRoot');
        $refMethod->setAccessible(true);

        $root = new TagNode(null, 'root');
        $intermediate = new TagNode($root, 'middle');
        $leaf = new TagNode($intermediate, 'leaf');
        $intermediate->addChild($leaf);
        $refMethod->invoke($intermediate, $root);

        $this->assertSame($root, $intermediate->getRoot());
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

        $this->assertSame(-1, $root->getIndexOf($leaf1));
        $this->assertSame(2, $intermediate->getIndexOf($leaf2));
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

        $this->assertSame($leaf1, $intermediate->getChild(0));
        $this->assertSame($leaf1, $intermediate->getChild(1));
        $this->assertSame($leaf2, $intermediate->getChild(2));
        $this->assertSame($leaf2, $intermediate->getChild(3));
    }

    /**
     * @expectedException \Exception
     */
    public function testGetChildException(): void
    {
        $root = new TagNode(null, 'root');
        $intermediate = new TagNode($root, 'middle');
        $root->addChild($intermediate);

        try {
            $root->getChild(5);
        } catch (\Exception $e) {
            $this->assertInstanceOf(\OutOfBoundsException::class, $e);

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

        // $intermediate and $leaf are added to $root twice in these tests.
        $this->assertSame(4, $root->getNumChildren());
        $this->assertSame(0, $leaf->getNumChildren());
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
        $this->assertSame($intermediate, $iterator->current());
        $this->assertTrue($iterator->valid());
    }

    public function testGetQName(): void
    {
        $root = new TagNode(null, 'rOoT');

        $this->assertSame('root', $root->getQName());
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
        $refMethod = new \ReflectionMethod(TagNode::class, 'isSimilarTag');
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
        $expected = '<table width="500" border="1">';
        $attrs = [
            'width'  => '500',
            'border' => '1',
        ];

        $root = new TagNode(null, 'table', $attrs);
        $intermediate = new TagNode(null, 'middle');

        $this->assertSame('<middle>', $intermediate->getOpeningTag());
        $this->assertSame($expected, $root->getOpeningTag());
        $this->assertSame($expected, $root->__toString());
    }

    public function testGetEndTag(): void
    {
        $root = new TagNode(null, 'root');
        $intermediate = new TagNode($root, 'middle');
        $root->addChild($intermediate);

        $this->assertSame('</middle>', $intermediate->getEndTag());
        $this->assertSame('</root>', $root->getEndTag());
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

        $copy = $root->copyTree();

        $this->assertEquals($root, $copy);
        $this->assertNotSame($root, $copy);
    }

    public function testGetMatchRatio(): void
    {
        $root = new TagNode(null, 'root');
        $textNode = new TextNode($root, 'Content of the root node');
        $intermediate = new TagNode($root, 'root');
        $text = new TextNode($intermediate, 'Content of the intermediate node');

        $this->assertSame('Content of the root node', $textNode->__toString());
        $this->assertSame('Content of the intermediate node', $text->__toString());
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

        $this->assertSame($leaf1, $root->getLeftMostChild());
        $this->assertSame($leaf2, $root->getRightMostChild());
    }

    public function testIsPre(): void
    {
        $root = new TagNode(null, 'pre');
        $intermediate = new TagNode($root, 'middle');

        $this->assertTrue($root->isPre());
        $this->assertFalse($intermediate->isPre());
    }

    public function testEqualsSameRoot()
    {
        $root = new TagNode(null, 'root');
        $left = new TagNode($root, 'leaf');
        $right = new TagNode($root, 'leaf');

        $this->assertFalse($left->equals($right));
    }

    public function testSplitUntil1()
    {
        $root = new TagNode(null, 'ol');
        $tag1 = new TagNode($root, 'li');
        new TagNode($tag1, 'p');
        $tag3 = new TagNode($tag1, 'p');
        new TagNode($tag1, 'p');

        $split = $root->splitUntil($root, $tag1, true);
        $this->assertFalse($split);

        $split = $tag1->splitUntil($root, $tag3, true);
        $this->assertTrue($split);

        $split = $tag1->splitUntil($root, $tag3, false);
        $this->assertTrue($split);

        $this->assertEquals(4, $root->getNumChildren());
    }

    public function testHasSameAttributes(): void
    {
        $refMethod = new \ReflectionMethod(TagNode::class, 'hasSameAttributes');
        $refMethod->setAccessible(true);

        $attrs1 = [
            'first'  => 'a',
            'second' => 'b',
        ];
        $attrs2 = [
            'second' => 'b',
            'first'  => 'a',
        ];
        $attrs3 = [
            'first' => 'a',
        ];

        $this->assertTrue($attrs1 == $attrs2);
        $this->assertFalse($attrs1 == $attrs3);

        $node = new TagNode(null, 'test', $attrs1);

        $this->assertTrue($refMethod->invoke($node, $attrs2));
        $this->assertFalse($refMethod->invoke($node, $attrs3));
    }

    public function testExpandWhiteSpace(): void
    {
        $p = new TagNode(null, 'p');
        new TextNode($p, 'This is a');
        $b = new TagNode($p, 'b');
        new TextNode($b, 'bold');
        new TextNode($p, 'test');

        $b->setWhiteBefore(true);
        $b->setWhiteAfter(true);

        $p->expandWhiteSpace();

        $this->assertInstanceOf(WhiteSpaceNode::class, $p->getChild(1));
        $this->assertInstanceOf(WhiteSpaceNode::class, $p->getChild(3));
    }

    public function testGetMinimalDeletedSet(): void
    {
        $table = new TagNode(null, 'table');

        $this->assertSame([], $table->getMinimalDeletedSet(0));

        $tr = new TagNode($table, 'tr');
        $td1 = new TagNode($tr, 'td');
        $td1test = new TextNode($td1, 'test');

        $modification = new Modification(ModificationType::REMOVED, ModificationType::REMOVED);
        $modification->setId(1);
        $td1test->setModification($modification);

        $this->assertSame([], $table->getMinimalDeletedSet(0));
        $this->assertSame([$table], $table->getMinimalDeletedSet(1));

        $td2 = new TagNode($tr, 'td');
        new TextNode($td2, 'test');

        $this->assertSame([$td1], $table->getMinimalDeletedSet(1));
    }
}
