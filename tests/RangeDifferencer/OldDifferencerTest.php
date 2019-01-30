<?php
/**
 * (c) Steve Nebes <snebes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace DaisyDiff\RangeDifferencer;

use DaisyDiff\Tag\TagComparator;
use PHPUnit\Framework\TestCase;

/**
 * OldDifferencer Tests.
 */
class OldDifferencerTest extends TestCase
{
    public function testFindDifferencesExample1(): void
    {
        $oldText = '<p> This is a blue book</p>';
        $newText = '<p> This is a <b>big</b> blue book</p>';
        $left = new TagComparator($oldText);
        $right = new TagComparator($newText);

        $ranges = OldDifferencer::findDifferences($left, $right);

        $this->assertSame('Left: (8, 0) Right: (8, 4)', $ranges[0]->__toString());
    }

    public function testFindDifferencesExample2(): void
    {
        $oldText = '<p> This is a blue book</p>';
        $newText = '<p> This is a <b>big</b> blue book</p>';
        $left = new TagComparator($oldText);
        $right = new TagComparator($newText);

        $ranges = OldDifferencer::findDifferences($right, $left);

        $this->assertSame('Left: (8, 4) Right: (8, 0)', $ranges[0]->__toString());
    }

    public function testFindDifferencesExample3(): void
    {
        $oldText = '<p> This is a blue book</p>';
        $left = new TagComparator($oldText);
        $right = new TagComparator($oldText);

        $ranges = OldDifferencer::findDifferences($right, $left);

        $this->assertCount(0, $ranges);
    }
}
