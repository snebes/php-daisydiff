<?php
/**
 * (c) Steve Nebes <snebes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace DaisyDiff\Html;

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

        $expected = <<<HTML
<p> This is a <ins class="diff-html-added">big </ins>blue book</p>
HTML;

        $this->assertXmlStringEqualsXmlString($expected, $result);
    }

//    public function testSimpleTextAddWithAncestor(): void
//    {
//        $ancestor = '<p> This is a book</p>';
//        $oldText = '<p> This is a blue book</p>';
//        $newText = '<p> This is a big blue book</p>';
//        $result = HtmlTestFixture::diff3($ancestor, $oldText, $newText);
//
//        $expected = <<<XML
//<p> This is a <ins class="diff-html-added">big blue </ins><ins class="diff-html-added">blue </ins>book</p>
//XML;
//
//        $this->assertXmlStringEqualsXmlString($expected, $result);
//    }

    public function testSimpleTextRemove(): void
    {
        $oldText = '<p> This is a blue book</p>';
        $newText = '<p> This is a book</p>';
        $result = HtmlTestFixture::diff($oldText, $newText);

        $expected = <<<HTML
<p> This is a <del class="diff-html-removed">blue </del>book</p>
HTML;

        $this->assertSame($expected, $result);
    }

//    public function testSimpleTextRemoveWithAncestor(): void
//    {
//        $ancestor = '<p> This is a big blue book</p>';
//        $oldText  = '<p> This is a blue book</p>';
//        $newText  = '<p> This is a book</p>';
//
//        $result = HtmlTestFixture::diff3($ancestor, $oldText, $newText);
//        $this->assertContains('<del class="diff-html-removed"', $result);
//    }

    public function testSimpleTextChange(): void
    {
        $oldText = '<p> This is a blue book</p>';
        $newText = '<p> This is a green book</p>';
        $result = HtmlTestFixture::diff($oldText, $newText);

        $expected = <<<HTML
<p> This is a <del class="diff-html-removed">blue </del><ins class="diff-html-added">green </ins>book</p>
HTML;

        $this->assertSame($expected, $result);
    }

//    public function testSimpleTextChangeWithAncestor(): void
//    {
//        $ancestor = '<p>This is a red book</p>';
//        $oldText  = '<p> This is a blue book</p>';
//        $newText  = '<p> This is a green book</p>';
//
//        $result = HtmlTestFixture::diff3($ancestor, $oldText, $newText);
//        $this->assertContains('<del class="diff-html-removed"', $result);
//        $this->assertContains('<ins class="diff-html-added"', $result);
//    }

    public function testAttributeAdd(): void
    {
        $oldText = '<p> This is a blue book</p>';
        $newText = '<p id="sample"> This is a blue book</p>';
        $result = HtmlTestFixture::diff($oldText, $newText);

        $expected = <<<HTML
<p id="sample"><span class="diff-html-changed"> </span><span class="diff-html-changed">This is a blue book</span></p>
HTML;

        $this->assertSame($expected, $result);
    }

//    public function testSimpleAttributeAddWithAncestor(): void
//    {
//        $ancestor = '<p class="example"> This is a blue book</p>';
//        $oldText  = '<p> This is a blue book</p>';
//        $newText  = '<p id="sample"> This is a blue book</p>';
//
//        $result = HtmlTestFixture::diff3($ancestor, $oldText, $newText);
//        $this->assertContains('<span class="diff-html-changed"', $result);
//    }

    public function testTagAdd(): void
    {
        $oldText = '<p> This is a blue book</p>';
        $newText = '<p> This is a <b>blue</b> book</p>';
        $result = HtmlTestFixture::diff($oldText, $newText);

        $expected = <<<HTML
<p> This is a <b><span class="diff-html-changed">blue</span></b><span class="diff-html-changed"> </span>book</p>
HTML;

        $this->assertSame($expected, $result);
    }

//    public function testTagAddWithAncestor(): void
//    {
//        $ancestor = 'This is a <b>blue</b> book';
//        $oldText  = '<p> This is a blue book</p>';
//        $newText  = '<p> This is a <b>blue</b> book</p>';
//
//        $result = HtmlTestFixture::diff3($ancestor, $oldText, $newText);
//        $this->assertContains('<span class="diff-html-changed"', $result);
//    }

    public function testTwiceChangeText(): void
    {
        $oldText = '<p> This is a blue book</p>';
        $newText = '<p> This is a red table</p>';
        $result = HtmlTestFixture::diff($oldText, $newText);

        $expected = <<<HTML
<p> This is a <del class="diff-html-removed">blue book</del><ins class="diff-html-added">red table</ins></p>
HTML;

        $this->assertSame($expected, $result);
    }

//    // TODO: check this vs. java version.
//    public function testTwiceChangeTextWithAncestor(): void
//    {
//        $ancestor = '<p> This is a blue book</p>';
//        $oldText  = '<p> This is a blue book</p>';
//        $newText  = '<p> This is a red table</p>';
//
//        $result = HtmlTestFixture::diff3($ancestor, $oldText, $newText);
//        $this->assertContains('<del class="diff-html-removed"', $result);
//        $this->assertContains('<ins class="diff-html-added"', $result);
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
     * @expectedException  \Exception
     */
    public function testScoreException(): void
    {
        try {
            HtmlDiffer::score(1, 2);
        } catch (\Exception $e) {
            $this->assertInstanceOf(\OutOfRangeException::class, $e);

            throw $e;
        }
    }
}
