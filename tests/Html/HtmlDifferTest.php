<?php declare(strict_types=1);

namespace DaisyDiff\Html;

use OutOfBoundsException;
use PHPUnit\Framework\TestCase;

/**
 * HtmlDiffer Tests.
 */
class HtmlDifferTest extends TestCase
{
    public function testSimpleTextAdd(): void
    {
        $oldText = '<p> This is a blue book</p>';
        $newText = '<p> This is a big blue book</p>';

        $result = HtmlTestFixture::diff($oldText, $newText);

        $this->assertContains('<p> This is a <ins class="diff-html-added"', $result);
    }

//    public function simpleTextAddWithAncestor(): void
//    {
//        $ancestor = '<p> This is a book</p>';
//        $oldText  = '<p> This is a blue book</p>';
//        $newText  = '<p> This is a big blue book</p>';
//
//        $result = HtmlTestFixture::diff3($ancestor, $oldText, $newText);
//    }

//    public function testSimpleTextRemove(): void
//    {
//        $oldText = '<p> This is a blue book</p>';
//        $newText = '<p> This is a book</p>';
//
//        $result = HtmlTestFixture::diff($oldText, $newText);
//
//        $this->assertContains('<p> This is a <del class="diff-html-removed"', $result);
//    }
//
//    public function testSimpleTextChange(): void
//    {
//        $oldText = '<p> This is a blue book</p>';
//        $newText = '<p> This is a green book</p>';
//
//        $result = HtmlTestFixture::diff($oldText, $newText);
//
//        $this->assertContains('<p> This is a <del class="diff-html-removed"', $result);
//        $this->assertContains('blue </del><ins class="diff-html-added"', $result);
//    }
//
//    public function testAttributeAdd(): void
//    {
//        $oldText = '<p> This is a blue book</p>';
//        $newText = '<p id="sample"> This is a blue book</p>';
//
//        $result = HtmlTestFixture::diff($oldText, $newText);
//
//        $this->assertContains('<span class="diff-html-changed"', $result);
//    }
//
//    public function testTagAdd(): void
//    {
//        $oldText = '<p> This is a blue book</p>';
//        $newText = '<p> This is a <b>blue</b> book</p>';
//
//        $result = HtmlTestFixture::diff($oldText, $newText);
//
//        $this->assertContains('<p> This is a <b><span class="diff-html-changed"', $result);
//    }
//
//    public function testTwiceChangeText(): void
//    {
//        $oldText = '<p> This is a blue book</p>';
//        $newText = '<p> This is a red table</p>';
//
//        $result = HtmlTestFixture::diff($oldText, $newText);
//
//        $this->assertContains('<p> This is a <del class="diff-html-removed"', $result);
//        $this->assertContains('<ins class="diff-html-added"', $result);
//    }
//
//    public function testScore(): void
//    {
//        $this->assertEquals(2.7, HtmlDiffer::score(2, 5, 10), '', 0.5);
//        $this->assertEquals(0, HtmlDiffer::score(0, 0, 4, 20), '', 0.5);
//        $this->assertEquals(0, HtmlDiffer::score(1, 2, 0, 0, 9), '', 0.5);
//        $this->assertEquals(0.6, HtmlDiffer::score(0, 2, 0, 2), '', 0.5);
//        $this->assertEquals(3.3, HtmlDiffer::score(7, 6, 8), '', 0.5);
//    }
//
//    /**
//     * @expectedException OutOfBoundsException
//     */
//    public function testScoreException(): void
//    {
//        try {
//            HtmlDiffer::score(1, 2);
//        } catch (OutOfBoundsException $e) {
//            $this->assertEquals('Need at least 3 numbers.', $e->getMessage());
//            throw $e;
//        }
//    }
}
