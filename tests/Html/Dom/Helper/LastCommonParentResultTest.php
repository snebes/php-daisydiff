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
    /**
     * @covers DaisyDiff\Html\Dom\Helper\LastCommonParentResult::getLastCommonParent
     * @covers DaisyDiff\Html\Dom\Helper\LastCommonParentResult::setLastCommonParent
     */
    public function testSetLastCommonParent(): void
    {
        $root = new TagNode(null, 'root');

        $common = new LastCommonParentResult();
        $common->setLastCommonParent($root);
        $this->assertEquals($root, $common->getLastCommonParent());

        $common->setLastCommonParent(null);
        $this->assertNull($common->getLastCommonParent());
    }

    /**
     * @covers DaisyDiff\Html\Dom\Helper\LastCommonParentResult::isSplittingNeeded
     * @covers DaisyDiff\Html\Dom\Helper\LastCommonParentResult::setSplittingNeeded
     */
    public function testIsSplittingNeeded(): void
    {
        $common = new LastCommonParentResult();

        $this->assertFalse($common->isSplittingNeeded());
        $common->setSplittingNeeded();
        $this->assertTrue($common->isSplittingNeeded());
    }

    /**
     * @covers DaisyDiff\Html\Dom\Helper\LastCommonParentResult::getLastCommonParentDepth
     * @covers DaisyDiff\Html\Dom\Helper\LastCommonParentResult::setLastCommonParentDepth
     */
    public function testLastCommonParentDepth(): void
    {
        $common = new LastCommonParentResult();

        $this->assertEquals(-1, $common->getLastCommonParentDepth());
        $common->setLastCommonParentDepth(2);
        $this->assertEquals(2, $common->getLastCommonParentDepth());
    }

    /**
     * @covers DaisyDiff\Html\Dom\Helper\LastCommonParentResult::getIndexInLastCommonParent
     * @covers DaisyDiff\Html\Dom\Helper\LastCommonParentResult::setIndexInLastCommonParent
     */
    public function testIndexInLastCommonParentDepth(): void
    {
        $common = new LastCommonParentResult();

        $this->assertEquals(-1, $common->getIndexInLastCommonParent());
        $common->setIndexInLastCommonParent(2);
        $this->assertEquals(2, $common->getIndexInLastCommonParent());
    }
}
