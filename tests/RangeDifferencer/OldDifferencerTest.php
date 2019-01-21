<?php declare(strict_types=1);

namespace DaisyDiff\RangeDifferencer;

use DaisyDiff\Tag\TagComparator;
use PHPUnit\Framework\TestCase;

/**
 * OldDifferencer Tests.
 */
class OldDifferencerTest extends TestCase
{
    protected function setUp()
    {
        $this->markTestSkipped();
    }

    public function testFindDifferencesExample1(): void
    {
        $oldText = '<p> This is a blue book</p>';
        $newText = '<p> This is a <b>big</b> blue book</p>';

        $left  = new TagComparator($oldText);
        $right = new TagComparator($newText);

        $differenceRanges = OldDifferencer::findDifferences($left, $right);
        $this->assertContains('Left: (8, 0) Right: (8, 4)', strval($differenceRanges[0]));
    }

    public function testFindDifferencesExample2(): void
    {
        $oldText = '<p> This is a blue book</p>';
        $newText = '<p> This is a <b>big</b> blue book</p>';

        $left  = new TagComparator($oldText);
        $right = new TagComparator($newText);

        $differenceRanges = OldDifferencer::findDifferences($right, $left);
        $this->assertContains('Left: (8, 4) Right: (8, 0)', strval($differenceRanges[0]));
    }

    public function testFindDifferencesExample3(): void
    {
        $oldText = '<p> This is a blue book</p>';

        $left  = new TagComparator($oldText);
        $right = new TagComparator($oldText);

        $differenceRanges = OldDifferencer::findDifferences($right, $left);
        $this->assertEquals(0, count($differenceRanges));
    }
}
