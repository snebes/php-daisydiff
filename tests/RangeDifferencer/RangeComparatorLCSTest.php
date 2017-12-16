<?php declare(strict_types=1);

namespace DaisyDiff\RangeDifferencer;

use DaisyDiff\Tag\TagComparator;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use ReflectionProperty;

/**
 * RangeDifferenceLCS Tests.
 */
class RangeComparatorLCSTest extends TestCase
{
    public function testDifferencesIterator(): void
    {
        $oldText = '<p> This is a blue book</p>';
        $newText = '<p> This is a <b>big</b> blue book</p>';

        $left  = new TagComparator($oldText);
        $right = new TagComparator($newText);

        $settings = new LCSSettings();
        $rangeDifference = RangeComparatorLCS::findDifferences($settings, $left, $right);

        $this->assertEquals('Left: (8, 0) Right: (8, 4)', strval($rangeDifference[0]));
    }

    public function testLength(): void
    {
        $oldText = '<p> This is a blue book</p>';
        $newText = '<p> This is a <b>big</b> blue book</p>';

        $left  = new TagComparator($oldText);
        $right = new TagComparator($newText);

        $comp = new RangeComparatorLCS($left, $right);

        $this->assertEquals(0, $comp->getLength());
        $this->assertEquals(12, $comp->getLength1());
        $this->assertEquals(16, $comp->getLength2());
    }

    public function testLengthEmptyExample1(): void
    {
        $oldText = '';
        $newText = '<p> This is a <b>big</b> blue book</p>';

        $left  = new TagComparator($oldText);
        $right = new TagComparator($newText);

        $comp = new RangeComparatorLCS($left, $right);

        $this->assertEquals(0, $comp->getLength());
        $this->assertEquals(0, $comp->getLength1());
        $this->assertEquals(16, $comp->getLength2());
    }

    public function testLengthEmptyExample2(): void
    {
        $oldText = '';
        $newText = '';

        $left  = new TagComparator($oldText);
        $right = new TagComparator($newText);

        $comp = new RangeComparatorLCS($left, $right);

        $this->assertEquals(0, $comp->getLength());
        $this->assertEquals(0, $comp->getLength1());
        $this->assertEquals(0, $comp->getLength2());
    }

    public function testLengthEmptyExample3(): void
    {
        $oldText = '<p> This is a blue book</p>';
        $newText = '';

        $left  = new TagComparator($oldText);
        $right = new TagComparator($newText);

        $comp = new RangeComparatorLCS($left, $right);

        $this->assertEquals(0, $comp->getLength());
        $this->assertEquals(12, $comp->getLength1());
        $this->assertEquals(0, $comp->getLength2());
    }

    public function testInitializeLCS(): void
    {
        $oldText = '<p> This is a blue book</p>';
        $newText = '<p> This is a <b>big</b> blue book</p>';

        $left  = new TagComparator($oldText);
        $right = new TagComparator($newText);

        $comp = new RangeComparatorLCS($left, $right);

        $refMethod = new ReflectionMethod($comp, 'initializeLcs');
        $refMethod->setAccessible(true);
        $refMethod->invoke($comp, 20);

        $refProp = new ReflectionProperty($comp, 'lcs');
        $refProp->setAccessible(true);
        $lcs = $refProp->getValue($comp);

        $this->assertEquals(2, count($lcs));
        $this->assertEquals(20, count($lcs[0]));
    }

    public function testInitializeLCSZero(): void
    {
        $oldText = '<p> This is a blue book</p>';
        $newText = '<p> This is a <b>big</b> blue book</p>';

        $left  = new TagComparator($oldText);
        $right = new TagComparator($newText);

        $comp = new RangeComparatorLCS($left, $right);

        $refMethod = new ReflectionMethod($comp, 'initializeLcs');
        $refMethod->setAccessible(true);
        $refMethod->invoke($comp, 0);

        $refProp = new ReflectionProperty($comp, 'lcs');
        $refProp->setAccessible(true);
        $lcs = $refProp->getValue($comp);

        $this->assertEquals(2, count($lcs));
        $this->assertEquals(0, count($lcs[0]));
    }

    public function testIsRangeEqual(): void
    {
        $oldText = '<p> This is a blue book</p>';
        $newText = '<p> This is a <b>big</b> blue book</p>';

        $left  = new TagComparator($oldText);
        $right = new TagComparator($newText);

        $comp = new RangeComparatorLCS($left, $right);

        $refMethod = new ReflectionMethod($comp, 'isRangeEqual');
        $refMethod->setAccessible(true);

        $this->assertTrue($refMethod->invoke($comp,0, 0));
        $this->assertFalse($refMethod->invoke($comp,0, 3));
    }

    public function testGetDifferencesExample1(): void
    {
        $oldText = "<p> This is a blue book</p> \n <div style=\"example\">This book is about food</div>";
        $newText = '<p> This is a <b>big</b> blue book</p>';

        $left  = new TagComparator($oldText);
        $right = new TagComparator($newText);

        $comp = new RangeComparatorLCS($left, $right);

        $diff = $comp->getDifferences();

        $this->assertEquals(1, count($diff));
        $this->assertEquals('Left: (0, 26) Right: (0, 16)', strval($diff[0]));
    }

    public function testGetDifferencesExample2(): void
    {
        $oldText = '<div style="example">This book is about food</div>';
        $newText = '<p> This is a <b>big</b> blue book</p>';

        $left  = new TagComparator($oldText);
        $right = new TagComparator($newText);

        $comp = new RangeComparatorLCS($left, $right);

        $rangeDifference = $comp->getDifferences();

        $this->assertEquals(1, count($rangeDifference));
        $this->assertEquals('Left: (0, 11) Right: (0, 16)', strval($rangeDifference[0]));
    }

    public function testGetDifferencesExample3(): void
    {
        $newText = '<p> This is a <b>big</b> blue book</p>';
        $right = new TagComparator($newText);

        $comp = new RangeComparatorLCS($right, $right);

        $rangeDifference = $comp->getDifferences();

        $this->assertEquals(1, count($rangeDifference));
        $this->assertEquals('Left: (0, 16) Right: (0, 16)', strval($rangeDifference[0]));
    }
}
