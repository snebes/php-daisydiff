<?php declare(strict_types=1);

namespace DaisyDiff\RangeDifferencer;

use DaisyDiff\RangeDifferencer\Core\LCSSettings;
use DaisyDiff\Tag\TagComparator;
use PHPUnit\Framework\TestCase;

/**
 * RangeDifferencer Tests.
 */
class RangeDifferencerTest extends TestCase
{
    public function testFindDifferenceExample1(): void
    {
        $oldText = '<p> This is a green book about food</p>';
        $newText = '<p> This is a <b>big</b> blue book</p>';

        $left  = new TagComparator($oldText);
        $right = new TagComparator($newText);

        $diff = RangeDifferencer::findDifferences($left, $right);

        $this->assertEquals(3, count($diff));
        $this->assertContains('Left: (8, 1) Right: (8, 3)', strval($diff[0]));
        $this->assertContains('Left: (10, 0) Right: (12, 2)', strval($diff[1]));
    }

    public function testFindDifferenceExample2(): void
    {
        $oldText = '<p> This is a green book about food</p>';
        $newText = '<p> This is a <b>big</b> blue book</p>';

        $left  = new TagComparator($oldText);
        $right = new TagComparator($newText);

        $settings = new LCSSettings();
        $diff = RangeDifferencer::findDifferences($left, $right, $settings);

        $this->assertEquals(3, count($diff));
        $this->assertContains('Left: (8, 1) Right: (8, 3)', strval($diff[0]));
        $this->assertContains('Left: (10, 0) Right: (12, 2)', strval($diff[1]));
    }

    public function testFindDifferenceExample3(): void
    {
        $oldText = '<p> This is a green book about food</p>';
        $newText = '<p> This is a <b>big</b> blue book</p>';

        $left  = new TagComparator($oldText);
        $right = new TagComparator($newText);

        $settings = new LCSSettings();
        $settings->setUseGreedyMethod(true);
        $diff = RangeDifferencer::findDifferences($left, $right, $settings);

        $this->assertEquals(2, count($diff));
        $this->assertContains('Left: (8, 1) Right: (8, 5)', strval($diff[0]));
        $this->assertContains('Left: (11, 4) Right: (15, 0)', strval($diff[1]));
    }

    public function testFindDifferenceExample4(): void
    {
        $ancestor = '<p> This is a book </p>';
        $oldText  = '<p> This is a green book about food</p>';
        $newText  = '<p> This is a <b>big</b> blue book</p>';

        $left  = new TagComparator($oldText);
        $right = new TagComparator($newText);
        $ancestorTag = new TagComparator($ancestor);

        $settings = new LCSSettings();
        $settings->setUseGreedyMethod(true);

        $diff = RangeDifferencer::findDifferences3($ancestorTag, $left, $right, $settings);

        $this->assertEquals(2, count($diff));
        $this->assertContains('Left: (8, 2) Right: (8, 6) Ancestor: (8, 0)', strval($diff[0]));
        $this->assertContains('Left: (11, 4) Right: (15, 0) Ancestor: (9, 1)', strval($diff[1]));
    }

    public function testFindDifferenceExample5(): void
    {
        $oldText = '<p> This is a <b>big</b> blue book</p>';
        $newText = '<p> This is a <b>big</b> blue book</p>';

        $left  = new TagComparator($oldText);
        $right = new TagComparator($newText);

        $settings = new LCSSettings();
        $settings->setUseGreedyMethod(true);
        $diff = RangeDifferencer::findDifferences3(null, $left, $right, $settings);

        $this->assertEquals(0, count($diff));
    }

    public function testFindDifferenceExample6(): void
    {
        $oldText = '<p> This is a green book about food</p>';
        $newText = '<p> This is a <b>big</b> blue book</p>';

        $left  = new TagComparator($oldText);
        $right = new TagComparator($newText);

        $settings = new LCSSettings();
        $settings->setUseGreedyMethod(true);

        $diff = RangeDifferencer::findRanges($left, $right, $settings);
        $this->assertEquals(5, count($diff));
        $this->assertContains('Left: (0, 8) Right: (0, 8)', strval($diff[0]));
        $this->assertContains('Left: (8, 1) Right: (8, 5)', strval($diff[1]));
    }

    public function testFindDifferenceExample7(): void
    {
        $oldText = '<p> This is a green book about food</p>';
        $newText = '<p> This is a <b>big</b> blue book</p>';

        $left  = new TagComparator($oldText);
        $right = new TagComparator($newText);

        $settings = new LCSSettings();
        $settings->setUseGreedyMethod(true);

        $diff = RangeDifferencer::findRanges($left, $right, $settings);
        $this->assertEquals(5, count($diff));
        $this->assertContains('Left: (0, 8) Right: (0, 8)', strval($diff[0]));
        $this->assertContains('Left: (8, 1) Right: (8, 5)', strval($diff[1]));
    }

    public function testFindDifferenceExample8(): void
    {
        $ancestor = '<p> This is a book </p>';
        $oldText  = '<p> This is a green book about food</p>';
        $newText  = '<p> This is a <b>big</b> blue book</p>';

        $left  = new TagComparator($oldText);
        $right = new TagComparator($newText);
        $ancestorTag = new TagComparator($ancestor);

        $settings = new LCSSettings();
        $settings->setUseGreedyMethod(true);

        $diff = RangeDifferencer::findRanges3($ancestorTag, $left, $right, $settings);
        $this->assertEquals(5, count($diff));
        $this->assertContains('Left: (0, 8) Right: (0, 8) Ancestor: (0, 8)', strval($diff[0]));
        $this->assertContains('Left: (8, 2) Right: (8, 6) Ancestor: (8, 0)', strval($diff[1]));
    }

    public function testFindDifferenceExample9(): void
    {
        $oldText = '<p> This is a green book about food</p>';
        $newText = '<p> This is a <b>big</b> blue book</p>';

        $left  = new TagComparator($oldText);
        $right = new TagComparator($newText);

        $settings = new LCSSettings();
        $settings->setUseGreedyMethod(true);

        $diff = RangeDifferencer::findRanges3(null, $left, $right, $settings);
        $this->assertEquals(5, count($diff));
        $this->assertContains('Left: (0, 8) Right: (0, 8)', strval($diff[0]));
        $this->assertContains('Left: (8, 1) Right: (8, 5)', strval($diff[1]));
    }
}
