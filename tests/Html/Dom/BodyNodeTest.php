<?php

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
        $body         = new BodyNode();
        $intermediate = new TagNode($body, 'middle');
        $body->addChild($intermediate);

        $leaf = new TagNode($intermediate, 'leaf');
        $intermediate->addChild($leaf);

        $this->assertEquals($body, $body->copyTree());
    }

    public function testGetMinimalDeletedSet(): void
    {
        $body         = new BodyNode();
        $intermediate = new TagNode($body, 'middle');
        $body->addChild($intermediate);

        $leaf = new TagNode($intermediate, 'leaf');
        $intermediate->addChild($leaf);

        $nodes = [];
        $this->assertEquals($nodes, $body->getMinimalDeletedSet(0));
        $this->assertEquals($nodes, $leaf->getMinimalDeletedSet(0));
    }
}
