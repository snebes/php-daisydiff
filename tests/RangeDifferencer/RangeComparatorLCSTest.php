<?php
/**
 * (c) Steve Nebes <snebes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace DaisyDiff\RangeDifferencer;

use DaisyDiff\RangeDifferencer\Core\LCSSettings;
use DaisyDiff\Tag\TagComparator;
use PHPUnit\Framework\TestCase;

/**
 * RangeDifferenceLCS Tests.
 */
class RangeComparatorLCSTest extends TestCase
{
    public function testDifferencesIterator(): void
    {
        $oldText = '<p> This is a blue book</p>';
        $newText = '<p> This is a <b>big</b> blue book</p>';
        $left = new TagComparator($oldText);
        $right = new TagComparator($newText);
        $settings = new LCSSettings();

        $diffs = RangeComparatorLCS::findDifferences($left, $right, $settings);
        $this->assertSame('Left: (8, 0) Right: (8, 4)', $diffs[0]->__toString());
    }

    /**
     * @param string $oldText
     * @param string $newText
     * @param int    $length
     * @param int    $length1
     * @param int    $length2
     *
     * @dataProvider lengthTests
     */
    public function testLength(string $oldText, string $newText, int $length, int $length1, int $length2): void
    {
        $left = new TagComparator($oldText);
        $right = new TagComparator($newText);

        $comp = new RangeComparatorLCS($left, $right);
        $this->assertSame($length, $comp->getLength());
        $this->assertSame($length1, $comp->getLength1());
        $this->assertSame($length2, $comp->getLength2());
    }

    public function lengthTests()
    {
        yield 'Length' => ['<p> This is a blue book</p>', '<p> This is a <b>big</b> blue book</p>', 0, 12, 16];
        yield 'LengthEmptyExample1' => ['', '<p> This is a <b>big</b> blue book</p>', 0, 0, 16];
        yield 'LengthEmptyExample2' => ['', '', 0, 0, 0];
        yield 'LengthEmptyExample3' => ['<p> This is a blue book</p>', '', 0, 12, 0];
    }

    /**
     * @param int $length
     *
     * @dataProvider lcsTests
     */
    public function testInitializeLCS(int $length): void
    {
        $oldText = '<p> This is a blue book</p>';
        $newText = '<p> This is a <b>big</b> blue book</p>';
        $left = new TagComparator($oldText);
        $right = new TagComparator($newText);

        $comp = new RangeComparatorLCS($left, $right);

        $refMethod = new \ReflectionMethod($comp, 'initializeLcs');
        $refMethod->setAccessible(true);
        $refMethod->invoke($comp, $length);

        $refProp = new \ReflectionProperty($comp, 'lcs');
        $refProp->setAccessible(true);
        $lcs = $refProp->getValue($comp);

        $this->assertCount(2, $lcs);
        $this->assertCount($length, $lcs[0]);
    }

    public function lcsTests()
    {
        yield 'InitializeLCS' => [20];
        yield 'InitializeLCSZero' => [0];
    }

    public function testIsRangeEqual(): void
    {
        $oldText = '<p> This is a blue book</p>';
        $newText = '<p> This is a <b>big</b> blue book</p>';
        $left = new TagComparator($oldText);
        $right = new TagComparator($newText);
        $comp = new RangeComparatorLCS($left, $right);

        $refMethod = new \ReflectionMethod($comp, 'isRangeEqual');
        $refMethod->setAccessible(true);

        $this->assertTrue($refMethod->invoke($comp, 0, 0));
        $this->assertFalse($refMethod->invoke($comp, 0, 3));
    }

    public function testGetDifferencesExample1(): void
    {
        $oldText = "<p> This is a blue book</p> \n <div style=\"example\">This book is about food</div>";
        $newText = '<p> This is a <b>big</b> blue book</p>';
        $left = new TagComparator($oldText);
        $right = new TagComparator($newText);

        $comp = new RangeComparatorLCS($left, $right);
        $diffs = $comp->getDifferences();

        $this->assertCount(1, $diffs);
        $this->assertSame('Left: (0, 26) Right: (0, 16)', $diffs[0]->__toString());
    }

    public function testGetDifferencesExample2(): void
    {
        $oldText = '<div style="example">This book is about food</div>';
        $newText = '<p> This is a <b>big</b> blue book</p>';
        $left = new TagComparator($oldText);
        $right = new TagComparator($newText);

        $comp = new RangeComparatorLCS($left, $right);
        $diffs = $comp->getDifferences();

        $this->assertCount(1, $diffs);
        $this->assertSame('Left: (0, 11) Right: (0, 16)', $diffs[0]->__toString());
    }

    public function testGetDifferencesExample3(): void
    {
        $newText = '<p> This is a <b>big</b> blue book</p>';
        $right = new TagComparator($newText);

        $comp = new RangeComparatorLCS($right, $right);
        $diffs = $comp->getDifferences();

        $this->assertCount(1, $diffs);
        $this->assertSame('Left: (0, 16) Right: (0, 16)', $diffs[0]->__toString());
    }
}
