<?php declare(strict_types=1);

namespace DaisyDiff\Html\Dom\Helper;

use DaisyDiff\Html\Dom\TagNode;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

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
        $this->assertEquals($root, $common->getLastCommonParent());

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

        $this->assertEquals(-1, $common->getLastCommonParentDepth());
        $common->setLastCommonParentDepth(2);
        $this->assertEquals(2, $common->getLastCommonParentDepth());
    }

    public function testIndexInLastCommonParentDepth(): void
    {
        $common = new LastCommonParentResult();

        $this->assertEquals(-1, $common->getIndexInLastCommonParent());
        $common->setIndexInLastCommonParent(2);
        $this->assertEquals(2, $common->getIndexInLastCommonParent());
    }
}
