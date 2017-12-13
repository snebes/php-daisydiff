<?php declare(strict_types=1);

namespace DaisyDiff\RangeDifferencer;

use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use ReflectionProperty;

/**
 * LCS Tests
 */
class LCSTest extends TestCase
{
    /**
     * @group unit
     */
    public function testLongestCommonSequence1(): void
    {
        $fixture = new LCSFixture(0, 0);

        $this->assertEquals(0, $fixture->getLength());
    }

    /**
     * @group unit
     */
    public function testLongestCommonSequence2(): void
    {
        $fixture  = new LCSFixture(0, 0);
        $settings = new LCSSettings();

        $this->assertEquals(0, $fixture->getLength1());
        $this->assertEquals(0, $fixture->getLength2());
        $this->assertEquals(0, $fixture->longestCommonSequence($settings));

        $fixture->setLength1(5);
        $this->assertEquals(0, $fixture->longestCommonSequence($settings));

        $fixture->setLength1(0);
        $fixture->setLength2(10);
        $this->assertEquals(0, $fixture->longestCommonSequence($settings));
    }

    /**
     * @group unit
     */
    public function testFindMiddleSnake1(): void
    {
        $fixture = new LCSFixture(5, 10);

        $V = array_fill(0, 2, array_fill(0, 10, 0));
        $snake = [0, 0, 0];

        $refMethod = new ReflectionMethod($fixture, 'findMiddleSnake');
        $refMethod->setAccessible(true);

//        $refMethod->invokeArgs($fixture, [0, 0, 0, 0, &$V, &$snake]);
    }
}
