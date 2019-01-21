<?php
/**
 * (c) Steve Nebes <snebes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace DaisyDiff\Html\Modification;

use PHPUnit\Framework\TestCase;

/**
 * ModificationType tests
 */
class ModificationTypeTest extends TestCase
{
    public function testTypes(): void
    {
        $this->assertSame('added', ModificationType::ADDED);
        $this->assertSame('removed', ModificationType::REMOVED);
        $this->assertSame('changed', ModificationType::CHANGED);
        $this->assertSame('conflict', ModificationType::CONFLICT);
        $this->assertSame('none', ModificationType::NONE);
    }
}