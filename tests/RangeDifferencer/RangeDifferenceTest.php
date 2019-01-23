<?php
/**
 * (c) Steve Nebes <snebes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace DaisyDiff\RangeDifferencer;

use PHPUnit\Framework\TestCase;

/**
 * RangeDifference Tests.
 */
class RangeDifferenceTest extends TestCase
{
    public function testRangeDifferenceExample1(): void
    {
        $difference = new RangeDifference(RangeDifference::LEFT);
        $this->assertSame(3, $difference->getKind());
    }

    public function testRangeDifferenceExample2(): void
    {
        $difference = new RangeDifference(RangeDifference::RIGHT, 0,12, 0, 16);

        $this->assertSame(2, $difference->getKind());
        $this->assertSame(16, $difference->getLeftLength());
        $this->assertSame(0, $difference->getLeftStart());
        $this->assertSame(12, $difference->getRightLength());
        $this->assertSame(0, $difference->getRightStart());
    }

    public function testRangeDifferenceExample3(): void
    {
        $difference = new RangeDifference(RangeDifference::CONFLICT, 0, 12, 0, 16, 0, 0);

        $this->assertSame(1, $difference->getKind());
        $this->assertSame(16, $difference->getLeftLength());
        $this->assertSame(0, $difference->getLeftStart());
        $this->assertSame(12, $difference->getRightLength());
        $this->assertSame(0, $difference->getRightStart());
        $this->assertSame(0, $difference->getAncestorLength());
        $this->assertSame(0, $difference->getAncestorStart());
    }

    public function testKind(): void
    {
        $difference = new RangeDifference(RangeDifference::ANCESTOR);
        $this->assertSame(4, $difference->getKind());
    }

    /**
     * @param string $method
     * @param int    $value
     *
     * @dataProvider valuesToTest
     */
    public function testValues(string $method, int $value): void
    {
        $difference = new RangeDifference(RangeDifference::NOCHANGE, 0, 12, 0, 16, 10, 32);
        $this->assertSame($value, $difference->$method());
    }

    public function valuesToTest()
    {
        yield 'getAncestorStart' => ['getAncestorStart', 10];
        yield 'getAncestorLength' => ['getAncestorLength', 32];
        yield 'getAncestorEnd' => ['getAncestorEnd', 42];
        yield 'getRightStart' => ['getRightStart', 0];
        yield 'getRightLength' => ['getRightLength', 12];
        yield 'getRightEnd' => ['getRightEnd', 12];
        yield 'getLeftStart' => ['getLeftStart', 0];
        yield 'getLeftLength' => ['getLeftLength', 16];
        yield 'getLeftEnd' => ['getLeftEnd', 16];
        yield 'getMaxLength' => ['getMaxLength', 32];
    }

    public function testEquals(): void
    {
        $difference1 = new RangeDifference(RangeDifference::CHANGE, 0, 12, 0, 16, 10, 32);
        $difference2 = new RangeDifference(RangeDifference::CONFLICT, 1, 10, 2, 26, 10, 3);

        $this->assertFalse($difference1->equals($difference2));
        $this->assertTrue($difference1->equals($difference1));
    }

    public function testToString(): void
    {
        $difference1 = new RangeDifference(RangeDifference::CHANGE, 0, 12, 0, 16, 10, 32);
        $difference2 = new RangeDifference(RangeDifference::ANCESTOR, 0, 12, 0, 16, 0, 10);
        $difference3 = new RangeDifference(RangeDifference::CONFLICT, 0, 12, 0, 16, 0, 0);

        $this->assertSame('Left: (0, 16) Right: (0, 12) Ancestor: (10, 32)', $difference1->__toString());
        $this->assertSame('Left: (0, 16) Right: (0, 12) Ancestor: (0, 10)', $difference2->__toString());
        $this->assertSame('Left: (0, 16) Right: (0, 12)', $difference3->__toString());
    }
}
