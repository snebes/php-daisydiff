<?php
/**
 * (c) Steve Nebes <snebes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace SN\DaisyDiff\RangeDifferencer;

use SN\DaisyDiff\RangeDifferencer\Core\LCSSettings;
use SN\DaisyDiff\Tag\TagComparator;
use PHPUnit\Framework\TestCase;

/**
 * RangeDifferencer Tests.
 */
class RangeDifferencerTest extends TestCase
{
    /** @var TagComparator */
    private $left;

    /** @var TagComparator */
    private $right;

    protected function setUp()
    {
        $this->left = new TagComparator('<p> This is a green book about food</p>');
        $this->right = new TagComparator('<p> This is a <b>big</b> blue book</p>');
    }

    public function testFindDifferenceExample1(): void
    {
        $diffs = RangeDifferencer::findDifferences($this->left, $this->right);

        $this->assertCount(3, $diffs);
        $this->assertSame('Left: (8, 1) Right: (8, 3)', $diffs[0]->__toString());
        $this->assertSame('Left: (10, 0) Right: (12, 2)', $diffs[1]->__toString());
    }

    public function testFindDifferenceExample2(): void
    {
        $settings = new LCSSettings();
        $diffs = RangeDifferencer::findDifferences($this->left, $this->right, $settings);

        $this->assertCount(3, $diffs);
        $this->assertSame('Left: (8, 1) Right: (8, 3)', $diffs[0]->__toString());
        $this->assertSame('Left: (10, 0) Right: (12, 2)', $diffs[1]->__toString());
    }

    public function testFindDifferenceExample3(): void
    {
        $settings = new LCSSettings();
        $settings->setUseGreedyMethod(true);
        $diffs = RangeDifferencer::findDifferences($this->left, $this->right, $settings);

        $this->assertCount(2, $diffs);
        $this->assertSame('Left: (8, 1) Right: (8, 5)', $diffs[0]->__toString());
        $this->assertSame('Left: (11, 4) Right: (15, 0)', $diffs[1]->__toString());
    }

    public function testFindDifferenceExample4(): void
    {
        $ancestor = new TagComparator('<p> This is a book </p>');

        $settings = new LCSSettings();
        $settings->setUseGreedyMethod(true);

        $diffs = RangeDifferencer::findDifferences3($ancestor, $this->left, $this->right, $settings);

        $this->assertCount(2, $diffs);
        $this->assertSame('Left: (8, 2) Right: (8, 6) Ancestor: (8, 0)', $diffs[0]->__toString());
        $this->assertSame('Left: (11, 4) Right: (15, 0) Ancestor: (9, 1)', $diffs[1]->__toString());
    }

    public function testFindDifferenceExample5(): void
    {
        $left = new TagComparator('<p> This is a <b>big</b> blue book</p>');

        $settings = new LCSSettings();
        $settings->setUseGreedyMethod(true);

        $diffs = RangeDifferencer::findDifferences($left, $this->right, $settings);

        $this->assertCount(0, $diffs);
    }

    public function testFindDifferenceExample6(): void
    {
        $settings = new LCSSettings();
        $settings->setUseGreedyMethod(true);

        $diffs = RangeDifferencer::findRanges($this->left, $this->right, $settings);

        $this->assertCount(5, $diffs);
        $this->assertSame('Left: (0, 8) Right: (0, 8)', $diffs[0]->__toString());
        $this->assertSame('Left: (8, 1) Right: (8, 5)', $diffs[1]->__toString());
    }

    public function testFindDifferenceExample7(): void
    {
        $settings = new LCSSettings();
        $settings->setUseGreedyMethod(true);

        $diffs = RangeDifferencer::findRanges($this->left, $this->right, $settings);

        $this->assertCount(5, $diffs);
        $this->assertSame('Left: (0, 8) Right: (0, 8)', $diffs[0]->__toString());
        $this->assertSame('Left: (8, 1) Right: (8, 5)', $diffs[1]->__toString());
    }

    public function testFindDifferenceExample8(): void
    {
        $ancestor = new TagComparator('<p> This is a book </p>');

        $settings = new LCSSettings();
        $settings->setUseGreedyMethod(true);

        $diffs = RangeDifferencer::findRanges3($ancestor, $this->left, $this->right, $settings);

        $this->assertCount(5, $diffs);
        $this->assertSame('Left: (0, 8) Right: (0, 8) Ancestor: (0, 8)', $diffs[0]->__toString());
        $this->assertSame('Left: (8, 2) Right: (8, 6) Ancestor: (8, 0)', $diffs[1]->__toString());
    }

    public function testFindDifferenceExample9(): void
    {
        $settings = new LCSSettings();
        $settings->setUseGreedyMethod(true);

        $diffs = RangeDifferencer::findRanges($this->left, $this->right, $settings);

        $this->assertCount(5, $diffs);
        $this->assertSame('Left: (0, 8) Right: (0, 8)', $diffs[0]->__toString());
        $this->assertSame('Left: (8, 1) Right: (8, 5)', $diffs[1]->__toString());
    }
}
