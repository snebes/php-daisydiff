<?php
/**
 * (c) Steve Nebes <snebes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace SN\DaisyDiff\Html\Dom\Helper;

use SN\DaisyDiff\Html\Dom\TagNode;
use PHPUnit\Framework\TestCase;

/**
 * LastCommonParentResult Tests.
 */
class LastCommonParentResultTest extends TestCase
{
    public function testSetLastCommonParent(): void
    {
        $root = new TagNode(null, 'root');

        $common = new LastCommonParentResult();
        $common->setLastCommonParent($root);
        $this->assertSame($root, $common->getLastCommonParent());

        $common->setLastCommonParent(null);
        $this->assertNull($common->getLastCommonParent());
    }

    public function testIsSplittingNeeded(): void
    {
        $common = new LastCommonParentResult();

        $this->assertFalse($common->isSplittingNeeded());
        $common->setSplittingNeeded();
        $this->assertTrue($common->isSplittingNeeded());
    }

    public function testLastCommonParentDepth(): void
    {
        $common = new LastCommonParentResult();

        $this->assertSame(-1, $common->getLastCommonParentDepth());
        $common->setLastCommonParentDepth(2);
        $this->assertSame(2, $common->getLastCommonParentDepth());
    }

    public function testIndexInLastCommonParent(): void
    {
        $common = new LastCommonParentResult();

        $this->assertSame(-1, $common->getIndexInLastCommonParent());
        $common->setIndexInLastCommonParent(2);
        $this->assertSame(2, $common->getIndexInLastCommonParent());
    }
}
