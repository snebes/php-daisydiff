<?php
/**
 * (c) Steve Nebes <snebes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace DaisyDiff\Html\Ancestor;

use DaisyDiff\Html\Modification\HtmlLayoutChange;
use PHPUnit\Framework\TestCase;

/**
 * AncestorComparatorResult Tests.
 */
class AncestorComparatorResultTest extends TestCase
{
    public function testIsChanged(): void
    {
        $result = new AncestorComparatorResult();
        $this->assertFalse($result->isChanged());
    }

    public function testSetChanged(): void
    {
        $result = new AncestorComparatorResult();
        $result->setChanged(true);

        $this->assertTrue($result->isChanged());
    }

    public function testGetChanges(): void
    {
        $result = new AncestorComparatorResult();
        $this->assertSame('', $result->getChanges());
    }

    public function testSetChanges(): void
    {
        $result = new AncestorComparatorResult();
        $result->setChanges('blue');

        $this->assertSame('blue', $result->getChanges());
    }

    public function testGetHtmlLayoutChanges(): void
    {
        $result = new AncestorComparatorResult();
        $this->assertSame([], $result->getHtmlLayoutChanges());
    }

    public function testSetHtmlLayoutChanges(): void
    {
        $result = new AncestorComparatorResult();

        $changes = [new HtmlLayoutChange()];
        $result->setHtmlLayoutChanges($changes);

        $this->assertSame($changes, $result->getHtmlLayoutChanges());
    }
}
