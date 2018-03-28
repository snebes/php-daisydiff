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
//        $output = HtmlTestFixture::diff3($ancestor, $oldText, $newText);
//    }

    public function testSimpleTextRemove(): void
    {
        $oldText = '<p> This is a blue book</p>';
        $newText = '<p> This is a book</p>';

        $result = HtmlTestFixture::diff($oldText, $newText);

        $this->assertContains('<p> This is a <del class="diff-html-removed"', $result);
    }

//    public function simpleTextRemoveWithAncestor(): void
//    {
//        $ancestor = '<p> This is a big blue book</p>';
//        $oldText  = '<p> This is a blue book</p>';
//        $newText  = '<p> This is a book</p>';
//
//        $output = HtmlTestFixture::diff3($ancestor, $oldText, $newText);
//
//        $this->assertContains('<span class="diff-html-removed"', $output);
//    }

    public function testSimpleTextChange(): void
    {
        $oldText = '<p> This is a blue book</p>';
        $newText = '<p> This is a green book</p>';

        $result = HtmlTestFixture::diff($oldText, $newText);

        $this->assertContains('<p> This is a <del class="diff-html-removed"', $result);
        $this->assertContains('blue </del><ins class="diff-html-added"', $result);
    }

//    public function simpleTextChangeWithAncestor(): void
//    {
//        $ancestor = '<p>This is a red book</p>';
//        $oldText  = '<p> This is a blue book</p>';
//        $newText  = '<p> This is a green book</p>';
//
//        $output = HtmlTestFixture::diff3($ancestor, $oldText, $newText);
//
//        $this->assertContains('<span class="diff-html-removed"', $output);
//        $this->assertContains('<span class="diff-html-added"', $output);
//    }

    public function testAttributeAdd(): void
    {
        $oldText = '<p> This is a blue book</p>';
        $newText = '<p id="sample"> This is a blue book</p>';

        $result = HtmlTestFixture::diff($oldText, $newText);

        $this->assertContains('<span class="diff-html-changed"', $result);
    }

//    public function simpleAttributeAddWithAncestor(): void
//    {
//        $ancestor = '<p class="example"> This is a blue book</p>';
//        $oldText  = '<p> This is a blue book</p>';
//        $newText  = '<p id="sample"> This is a blue book</p>';
//
//        $output = HtmlTestFixture::diff3($ancestor, $oldText, $newText);
//
//        $this->assertContains('<span class="diff-html-changed"', $output);
//    }

    public function testTagAdd(): void
    {
        $oldText = '<p> This is a blue book</p>';
        $newText = '<p> This is a <b>blue</b> book</p>';

        $result = HtmlTestFixture::diff($oldText, $newText);

        $this->assertContains('<p> This is a <b><span class="diff-html-changed"', $result);
    }

//    public function testTagAddWithAncestor(): void
//    {
//        $ancestor = 'This is a <b>blue</b> book';
//        $oldText  = '<p> This is a blue book</p>';
//        $newText  = '<p> This is a <b>blue</b> book</p>';
//
//        $output = HtmlTestFixture::diff3($ancestor, $oldText, $newText);
//
//        $this->assertContains('<p> This is a <b><span class="diff-html-changed"', $output);
//    }

    public function testTwiceChangeText(): void
    {
        $oldText = '<p> This is a blue book</p>';
        $newText = '<p> This is a red table</p>';

        $result = HtmlTestFixture::diff($oldText, $newText);

        $this->assertContains('<p> This is a <del class="diff-html-removed"', $result);
        $this->assertContains('<ins class="diff-html-added"', $result);
    }

//    public function testTwiceChangeTextWithAncestor(): void
//    {
//        $ancestor = '<p> This is a blue book</p>';
//        $oldText  = '<p> This is a blue book</p>';
//        $newText  = '<p> This is a red table</p>';
//
//        $output = HtmlTestFixture::diff3($ancestor, $oldText, $newText);
//
//        $this->assertContains('<del class="diff-html-removed"', $output);
//        $this->assertContains('<ins class="diff-html-added"', $output);
//    }

    public function testScore(): void
    {
        $this->assertEquals(2.7, HtmlDiffer::score(2, 5, 10), '', 0.5);
        $this->assertEquals(0, HtmlDiffer::score(0, 0, 4, 20), '', 0.5);
        $this->assertEquals(0, HtmlDiffer::score(1, 2, 0, 0, 9), '', 0.5);
        $this->assertEquals(0.6, HtmlDiffer::score(0, 2, 0, 2), '', 0.5);
        $this->assertEquals(3.3, HtmlDiffer::score(7, 6, 8), '', 0.5);
    }

    /**
     * @expectedException OutOfBoundsException
     */
    public function testScoreException(): void
    {
        try {
            HtmlDiffer::score(1, 2);
        } catch (OutOfBoundsException $e) {
            $this->assertEquals('Need at least 3 numbers.', $e->getMessage());
            throw $e;
        }
    }
}
