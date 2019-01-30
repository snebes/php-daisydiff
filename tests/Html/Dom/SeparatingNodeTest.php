<?php
/**
 * (c) Steve Nebes <snebes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace SN\DaisyDiff\Html\Dom;

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
