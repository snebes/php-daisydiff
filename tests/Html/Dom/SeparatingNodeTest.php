<?php declare(strict_types=1);

namespace DaisyDiff\Html\Dom;

use PHPUnit\Framework\TestCase;

/**
 * SeparatingNode Tests.
 *
 * @covers DaisyDiff\Html\Dom\SeparatingNode::__construct
 */
class SeparatingNodeTest extends TestCase
{
    /**
     * @covers DaisyDiff\Html\Dom\SeparatingNode::equals
     */
    public function testEquals(): void
    {
        $root = new TagNode(null, 'root');
        $body = new SeparatingNode($root);

        $this->assertFalse($body->equals(null));
        $this->assertTrue($body->equals($body));
    }
}
