<?php declare(strict_types=1);

namespace DaisyDiff\Html\Dom;

use PHPUnit\Framework\TestCase;

/**
 * SeparatingNode Tests.
 */
class SeparatingNodeTest extends TestCase
{
    public function testEquals(): void
    {
        $root = new TagNode(null, 'root');
        $body = new SeparatingNode($root);

        $this->assertFalse($body->equals(null));
        $this->assertTrue($body->equals($body));
    }
}
