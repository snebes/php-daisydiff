<?php

declare(strict_types=1);

namespace RangeDifferencer;

use DaisyDiff\RangeDifferencer\DifferencesIterator;
use DaisyDiff\RangeDifferencer\RangeDifferencer;
use DaisyDiff\Tag\TagComparator;
use PHPUnit\Framework\TestCase;

class DifferencesIteratorTest extends TestCase
{
    /**
     * @return RangeDifferencer[]
     */
    private function getDifferences(): array
    {
        $oldText = '<p> This is a blue book</p>';
        $newText = '<p> This is a <b>big</b> blue book</p>';

        $oldComp = new TagComparator($oldText);
        $newComp = new TagComparator($newText);

        return RangeDifferencer::findDifferences($oldComp, $newComp);
    }

    public function testDifferencesIterator(): void
    {
        $diffs    = $this->getDifferences();
        $iterator = new DifferencesIterator($diffs);

        $this->assertContains('Left: (8, 0) Right: (8, 4)', strval($diffs[0]));
        $this->assertEquals(1, $iterator->getIndex());
    }

    public function testDifferencesIteratorEmpty(): void
    {
        $iterator = new DifferencesIterator([]);
        $this->assertEquals(0, $iterator->getIndex());
    }

    public function testGetCount(): void
    {
        $diffs    = $this->getDifferences();
        $iterator = new DifferencesIterator($diffs);

        $this->assertEquals(0, $iterator->getCount());
    }

    public function testGetCountEmpty(): void
    {
        $iterator = new DifferencesIterator([]);

        $this->assertEquals(0, $iterator->getIndex());
    }

    public function testNext(): void
    {
        $diffs    = $this->getDifferences();
        $iterator = new DifferencesIterator($diffs);

        $this->assertContains('Left: (8, 0) Right: (8, 4)', strval($diffs[0]));

        $oldText = '<p> This is a green book about food</p>';
        $newText = '<p> This is a <b>big</b> blue book</p>';

        $oldComp = new TagComparator($oldText);
        $newComp = new TagComparator($newText);

        $iterator->next();
        $diffs    = RangeDifferencer::findDifferences($oldComp, $newComp);
        $iterator = new DifferencesIterator($diffs);
        $iterator->next();

        $this->assertContains('Left: (8, 1) Right: (8, 3)', strval($diffs[0]));
    }

    public function testNextNull(): void
    {
        $diffs    = $this->getDifferences();
        $iterator = new DifferencesIterator($diffs);

        $this->assertContains('Left: (8, 0) Right: (8, 4)', strval($diffs[0]));

        $iterator->next();
        $iterator = new DifferencesIterator([]);
        $iterator->next();

        $this->assertEquals(1, $iterator->getCount());
    }

    public function testOther(): void
    {
        $diffs = $this->getDifferences();
        $left  = new DifferencesIterator($diffs);
        $right = new DifferencesIterator($diffs);

        $this->assertEquals($right, $left->other($right, $left));
        $this->assertEquals($left, $right->other($right, $left));
    }

    public function testRemoveAll(): void
    {
        $diffs    = $this->getDifferences();
        $iterator = new DifferencesIterator($diffs);

        $iterator->removeAll();
        $this->assertEquals(0, $iterator->getCount());
    }

    public function testRemoveAllEmpty(): void
    {
        $iterator = new DifferencesIterator([]);

        $iterator->removeAll();
        $this->assertEquals(0, $iterator->getCount());
    }
}
