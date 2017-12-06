<?php declare(strict_types=1);

namespace DaisyDiff\RangeDifferencer;

use PHPUnit\Framework\TestCase;

/**
 * RangeDifference Tests.
 */
class RangeDifferenceTest extends TestCase
{
    public function testRangeDifferenceExample1(): void
    {
        $difference = new RangeDifference(RangeDifferenceType::LEFT);
        $this->assertEquals(3, $difference->kind());
    }

    public function testRangeDifferenceExample2(): void
    {
        $difference = new RangeDifference(RangeDifferenceType::RIGHT, 1,12, 0, 16);

        $this->assertEquals(2, $difference->kind());
        $this->assertEquals(16, $difference->leftLength());
        $this->assertEquals(0, $difference->leftStart());
        $this->assertEquals(12, $difference->rightLength());
        $this->assertEquals(1, $difference->rightStart());
    }

    public function testRangeDifferenceExample3(): void
    {
        $difference = new RangeDifference(RangeDifferenceType::CONFLICT, 0, 12, 0, 16, 0, 0);

        $this->assertEquals(1, $difference->kind());
        $this->assertEquals(16, $difference->leftLength());
        $this->assertEquals(0, $difference->leftStart());
        $this->assertEquals(12, $difference->rightLength());
        $this->assertEquals(0, $difference->rightStart());
        $this->assertEquals(0, $difference->ancestorLength());
        $this->assertEquals(0, $difference->ancestorStart());
    }

    public function testKind(): void
    {
        $difference = new RangeDifference(RangeDifferenceType::ANCESTOR);
        $this->assertEquals(4, $difference->kind());
    }

    public function testAncestorStart(): void
    {
        $difference = new RangeDifference(RangeDifferenceType::NOCHANGE, 0, 12, 0, 16, 10, 0);
        $this->assertEquals(10, $difference->ancestorStart());
    }

    public function testAncestorLength(): void
    {
        $difference = new RangeDifference(RangeDifferenceType::CHANGE, 0, 12, 0, 16, 10, 32);
        $this->assertEquals(32, $difference->ancestorLength());
    }

    public function testAncestorEnd(): void
    {
        $difference = new RangeDifference(RangeDifferenceType::ERROR, 0, 12, 0, 16, 10, 32);
        $this->assertEquals(42, $difference->ancestorEnd());
    }

    public function testRightStart(): void
    {
        $difference = new RangeDifference(RangeDifferenceType::LEFT, 0, 12, 0, 16, 10, 32);
        $this->assertEquals(0, $difference->rightStart());
    }

    public function testRightLength(): void
    {
        $difference = new RangeDifference(RangeDifferenceType::RIGHT, 0, 12, 0, 16, 10, 32);
        $this->assertEquals(12, $difference->rightLength());
    }

    public function testRightEnd(): void
    {
        $difference = new RangeDifference(RangeDifferenceType::CONFLICT, 0, 12, 0, 16, 10, 32);
        $this->assertEquals(12, $difference->rightEnd());
    }

    public function testLeftStart(): void
    {
        $difference = new RangeDifference(RangeDifferenceType::ERROR, 0, 12, 0, 16, 10, 32);
        $this->assertEquals(0, $difference->leftStart());
    }

    public function testLeftLength(): void
    {
        $difference = new RangeDifference(RangeDifferenceType::NOCHANGE, 0, 12, 0, 16, 10, 32);
        $this->assertEquals(16, $difference->leftLength());
    }

    public function testLeftEnd(): void
    {
        $difference = new RangeDifference(RangeDifferenceType::CHANGE, 0, 12, 0, 16, 10, 32);
        $this->assertEquals(16, $difference->leftEnd());
    }

    public function testMaxLength(): void
    {
        $difference = new RangeDifference(RangeDifferenceType::CHANGE, 0, 12, 0, 16, 10, 32);
        $this->assertEquals(32, $difference->maxLength());
    }

    public function testEquals(): void
    {
        $difference1 = new RangeDifference(RangeDifferenceType::CHANGE, 0, 12, 0, 16, 10, 32);
        $difference2 = new RangeDifference(RangeDifferenceType::CONFLICT, 1, 10, 2, 26, 10, 3);

        $this->assertFalse($difference1->equals($difference2));
        $this->assertTrue($difference1->equals($difference1));
    }

    public function testToString(): void
    {
        $difference1 = new RangeDifference(RangeDifferenceType::CHANGE, 0, 12, 0, 16, 10, 32);
        $difference2 = new RangeDifference(RangeDifferenceType::ANCESTOR, 0, 12, 0, 16, 0, 10);
        $difference3 = new RangeDifference(RangeDifferenceType::CONFLICT, 0, 12, 0, 16, 0, 0);

        $this->assertEquals('Left: (0, 16) Right: (0, 12) Ancestor: (10, 32)', strval($difference1));
        $this->assertEquals('Left: (0, 16) Right: (0, 12) Ancestor: (0, 10)', strval($difference2));
        $this->assertEquals('Left: (0, 16) Right: (0, 12)', strval($difference3));
    }
}
