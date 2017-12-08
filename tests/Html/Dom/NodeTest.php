<?php declare(strict_types=1);

namespace DaisyDiff\Html\Dom;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

/**
 * Node Tests.
 */
class NodeTest extends TestCase
{
    public function testGetParentTree(): void
    {
        $root = new TagNode(null, 'root');
        $intermediate = new TagNode($root, 'middle');
        $root->addChild($intermediate);

        $leaf = new TagNode($intermediate, 'leaf');
        $intermediate->addChild($leaf);

        $this->assertEquals([$root, $intermediate], $leaf->getParentTree());
    }

    public function testGetRoot(): void
    {
        $root = new TagNode(null, 'root');
        $intermediate = new TagNode($root, 'middle');
        $root->addChild($intermediate);

        $this->assertEquals($root, $intermediate->getRoot());
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testGetLastCommonParentNullException(): void
    {
        $root = new TagNode(null, 'root');
        $intermediate = new TagNode($root, 'middle');
        $root->addChild($intermediate);

        try {
            $root->getLastCommonParent(null);
        } catch (InvalidArgumentException $e) {
            $this->assertEquals('The given TextNode is null.', $e->getMessage());
            throw $e;
        }
    }

    public function testGetLastCommonParent(): void
    {
        $root = new TagNode(null, 'root');
        $intermediate = new TagNode($root, 'intermediate');
        $root->addChild($intermediate);

        $leaf1 = new TagNode($intermediate, 'leaf');
        $intermediate->addChild($leaf1);
        $leaf2 = new TagNode($intermediate, 'leaf');
        $intermediate->addChild($leaf2);

        $parent = new TagNode(null, 'parent');
        $middle = new TagNode($parent, 'middle');
        $parent->addChild($middle);

        $leafNode = new TagNode($middle, 'leaf');
        $middle->addChild($leafNode);

        $this->assertEquals($intermediate, $leaf1->getLastCommonParent($leaf2)->getLastCommonParent());
        $this->assertEquals(1, $leaf1->getLastCommonParent($leaf2)->getLastCommonParentDepth());
        $this->assertEquals(0, $leaf1->getLastCommonParent($leaf2)->getIndexInLastCommonParent());
        $this->assertEquals($intermediate, $leaf1->getLastCommonParent($leaf1)->getLastCommonParent());
        $this->assertEquals($parent, $leafNode->getLastCommonParent($intermediate)->getLastCommonParent());
        $this->assertEquals($root, $leaf2->getLastCommonParent($middle)->getLastCommonParent());
        $this->assertEquals($parent, $leafNode->getLastCommonParent($leaf2)->getLastCommonParent());
        $this->assertEquals($root, $intermediate->getLastCommonParent($leafNode)->getLastCommonParent());
    }

    public function testSetParentRoot(): void
    {
        $refMethod = new ReflectionMethod(Node::class, 'setRoot');
        $refMethod->setAccessible(true);

        $root = new TagNode(null, 'root');
        $middle = new TagNode($root, 'middle');
        $refMethod->invoke($middle, $root);

        $leaf = new TagNode($root, 'leaf');
        $leaf->setParent($middle);

        $this->assertEquals($leaf->getParent(), $middle);
        $leaf->setParent(null);
        $this->assertNull($leaf->getParent());
    }

    public function testInPre(): void
    {
        $preRoot = new TagNode(null, 'pre');
        $intermediate = new TagNode($preRoot, 'intermediate');
        $preRoot->addChild($intermediate);

        $leaf = new TagNode($intermediate, 'leaf');
        $intermediate->addChild($leaf);

        $this->assertTrue($leaf->inPre());

        $root = new TagNode(null, 'root');
        $middle = new TagNode($root, 'middle');
        $root->addChild($middle);

        $leafNode = new TagNode($middle, 'leaf');
        $middle->addChild($leafNode);

        $this->assertFalse($leafNode->inPre());
    }

    public function testIsWhiteBeforeAfter(): void
    {
        $root = new TagNode(null, 'root');
        $intermediate = new TagNode($root, 'intermediate');
        $root->addChild($intermediate);

        $this->assertFalse($root->isWhiteBefore());
        $intermediate->setWhiteBefore(true);
        $this->assertTrue($intermediate->isWhiteBefore());
        $root->setWhiteAfter(true);
        $this->assertTrue($root->isWhiteAfter());
    }

    public function testDetectIgnorableWhiteSpace(): void
    {
        $root = new TagNode(null, 'root');
        $root->detectIgnorableWhiteSpace();

        // Noop, fake it!
        $this->assertTrue(true);
    }
}
