<?php
/**
 * (c) Steve Nebes <snebes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace SN\DaisyDiff\RangeDifferencer;

use SN\DaisyDiff\Tag\TagComparator;
use PHPUnit\Framework\TestCase;

/**
 * DifferencesIterator Tests
 */
class DifferencesIteratorTest extends TestCase
{
    /** @var RangeDifference[] */
    private $ranges = [];

    protected function setUp()
    {
        $oldText = '<p> This is a blue book</p>';
        $newText = '<p> This is a <b>big</b> blue book</p>';
        $oldComp = new TagComparator($oldText);
        $newComp = new TagComparator($newText);

        $this->ranges = RangeDifferencer::findDifferences($oldComp, $newComp);
    }

    public function testDifferencesIterator(): void
    {
        $iterator = new DifferencesIterator($this->ranges);

        $this->assertSame('Left: (8, 0) Right: (8, 4)', $this->ranges[0]->__toString());
        $this->assertSame(1, $iterator->getIndex());
    }

    public function testDifferencesIteratorEmpty(): void
    {
        $iterator = new DifferencesIterator([]);

        $this->assertSame(0, $iterator->getIndex());
        $this->assertNull($iterator->getDifference());
    }

    public function testGetCount(): void
    {
        $iterator = new DifferencesIterator($this->ranges);

        $this->assertSame(0, $iterator->getCount());
    }

    public function testGetCountEmpty(): void
    {
        $iterator = new DifferencesIterator([]);

        $this->assertSame(0, $iterator->getCount());
    }

    public function testNext(): void
    {
        $iterator = new DifferencesIterator($this->ranges);

        $this->assertSame('Left: (8, 0) Right: (8, 4)', $this->ranges[0]->__toString());

        $oldText = '<p> This is a green book about food</p>';
        $newText = '<p> This is a <b>big</b> blue book</p>';
        $oldComp = new TagComparator($oldText);
        $newComp = new TagComparator($newText);

        $iterator->next();
        $diffs = RangeDifferencer::findDifferences($oldComp, $newComp);
        $iterator = new DifferencesIterator($diffs);
        $iterator->next();

        $this->assertSame('Left: (8, 1) Right: (8, 3)', $diffs[0]->__toString());
    }

    public function testNextNull(): void
    {
        $iterator = new DifferencesIterator($this->ranges);

        $this->assertSame('Left: (8, 0) Right: (8, 4)', $this->ranges[0]->__toString());

        $iterator->next();
        $iterator = new DifferencesIterator([]);
        $iterator->next();

        $this->assertSame(1, $iterator->getCount());
        $this->assertNull($iterator->getDifference());
    }

    public function testOther(): void
    {
        $left = new DifferencesIterator($this->ranges);
        $right = new DifferencesIterator($this->ranges);

        $this->assertSame($right, $left->other($right, $left));
        $this->assertSame($right, $left->other($left, $right));
        $this->assertSame($left, $right->other($right, $left));
        $this->assertSame($left, $right->other($left, $right));
    }

    public function testRemoveAll(): void
    {
        $iterator = new DifferencesIterator($this->ranges);

        $iterator->next();
        $this->assertSame(1, $iterator->getCount());

        $iterator->removeAll();
        $this->assertSame(0, $iterator->getCount());
    }

    public function testRemoveAllEmpty(): void
    {
        $iterator = new DifferencesIterator([]);

        $iterator->next();
        $this->assertSame(1, $iterator->getCount());

        $iterator->removeAll();
        $this->assertSame(0, $iterator->getCount());
    }

    public function testEmpty(): void
    {
        $iterator = new DifferencesIterator([]);

        $this->assertSame(0, $iterator->getCount());
        $this->assertSame(0, $iterator->getIndex());
        $this->assertNull($iterator->getDifference());
        $this->assertSame([], $iterator->getRange());

        // This is very weird behavior, but this is java implementation.
        $iterator->next();
        $this->assertSame(1, $iterator->getCount());
        $this->assertSame(0, $iterator->getIndex());
        $this->assertNull($iterator->getDifference());
        $this->assertSame([null], $iterator->getRange());
    }
}
