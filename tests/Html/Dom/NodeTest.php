<?php
/**
 * (c) Steve Nebes <snebes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace DaisyDiff\Html\Dom;

use PHPUnit\Framework\TestCase;

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

        $this->assertSame([$root, $intermediate], $leaf->getParentTree());
    }

    public function testGetRoot(): void
    {
        $root = new TagNode(null, 'root');
        $intermediate = new TagNode($root, 'middle');
        $root->addChild($intermediate);

        $this->assertSame($root, $intermediate->getRoot());
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

        $this->assertSame($intermediate, $leaf1->getLastCommonParent($leaf2)->getLastCommonParent());
        $this->assertSame(1, $leaf1->getLastCommonParent($leaf2)->getLastCommonParentDepth());
        $this->assertSame(0, $leaf1->getLastCommonParent($leaf2)->getIndexInLastCommonParent());
        $this->assertSame($intermediate, $leaf1->getLastCommonParent($leaf1)->getLastCommonParent());
        $this->assertSame($parent, $leafNode->getLastCommonParent($intermediate)->getLastCommonParent());
        $this->assertSame($root, $leaf2->getLastCommonParent($middle)->getLastCommonParent());
        $this->assertSame($parent, $leafNode->getLastCommonParent($leaf2)->getLastCommonParent());
        $this->assertSame($root, $intermediate->getLastCommonParent($leafNode)->getLastCommonParent());
    }

    public function testSetParentRoot(): void
    {
        $refMethod = new \ReflectionMethod(Node::class, 'setRoot');
        $refMethod->setAccessible(true);

        $root = new TagNode(null, 'root');
        $middle = new TagNode($root, 'middle');
        $refMethod->invoke($middle, $root);
        $leaf = new TagNode($root, 'leaf');
        $leaf->setParent($middle);

        $this->assertSame($middle, $leaf->getParent());
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
        $leaf = new TagNode($middle, 'leaf');
        $middle->addChild($leaf);

        $this->assertFalse($leaf->inPre());
    }

    public function testIsWhiteBeforeAfter(): void
    {
        $root = new TagNode(null, 'root');
        $intermediate = new TagNode($root, 'middle');
        $root->addChild($intermediate);

        $this->assertFalse($root->isWhiteBefore());
        $this->assertFalse($root->isWhiteAfter());

        $intermediate->setWhiteBefore(true);
        $root->setWhiteAfter(true);

        $this->assertTrue($intermediate->isWhiteBefore());
        $this->assertTrue($root->isWhiteAfter());
    }
}
