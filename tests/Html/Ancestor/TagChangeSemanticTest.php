<?php
/**
 * (c) Steve Nebes <snebes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace DaisyDiff\Html\Ancestor;

use PHPUnit\Framework\TestCase;

/**
 * TagChangeSemantic tests
 */
class TagChangeSemanticTest extends TestCase
{
    public function testTypes(): void
    {
        $this->assertSame('moved', TagChangeSemantic::MOVED);
        $this->assertSame('style', TagChangeSemantic::STYLE);
        $this->assertSame('unknown', TagChangeSemantic::UNKNOWN);
    }
}