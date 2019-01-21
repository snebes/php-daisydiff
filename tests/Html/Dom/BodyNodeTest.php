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
 * BodyNode Tests.
 */
class BodyNodeTest extends TestCase
{
    public function testCopyTreeWithNoChildren(): void
    {
        $body = new BodyNode();
        $this->assertEquals($body, $body->copyTree());
    }

    public function testCopyTreeWithChildren(): void
    {
        $body = new BodyNode();
        $intermediate = new TagNode($body, 'middle');
        $body->addChild($intermediate);
        $leaf = new TagNode($intermediate, 'leaf');
        $intermediate->addChild($leaf);

        $this->assertEquals($body, $body->copyTree());
    }

    public function testGetMinimalDeletedSet(): void
    {
        $body = new BodyNode();
        $intermediate = new TagNode($body, 'middle');
        $body->addChild($intermediate);
        $leaf = new TagNode($intermediate, 'leaf');
        $intermediate->addChild($leaf);

        $nodes = [];
        $this->assertSame($nodes, $body->getMinimalDeletedSet(0));
        $this->assertSame($nodes, $leaf->getMinimalDeletedSet(0));
    }
}
