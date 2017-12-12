<?php declare(strict_types=1);

namespace DaisyDiff\Html\Ancestor;

use DaisyDiff\Html\Dom\TagNode;
use DaisyDiff\RangeDifferencer\RangeDifference;
use DaisyDiff\RangeDifferencer\RangeDifferencer;
use PHPUnit\Framework\TestCase;

/**
 * ChangeText Tests.
 */
class ChangeTextTest extends TestCase
{
    public function testAddText(): void
    {
        $text = new ChangeText(10);
        $html = '<ul class="changelist"><li>Moved out of a <b>html page</b>.</li><li>Moved out of a <b>html document</b>.</li><li><b>!diff-root!</b> added.</li></ul>';
        $textInput = 'Moved out of a html page. Moved out of a html document';

        $changeText = new ChangeText(10);
        $newText = 'content';

        $text->addText($html);
        $text->addText($textInput);
        $changeText->addText($newText);

        $this->assertEquals(ChangeText::class, get_class($text));
        $this->assertEquals($newText, $changeText);
    }

    public function testAddHtml(): void
    {
        $text = new ChangeText(10);
        $html = '<ul class="changelist"><li>Moved out of a <b>html page</b>.</li><li>Moved out of a <b>html document</b>.</li><li><b>!diff-root!</b> added.</li></ul>';
        $content = '<ol><li>Moved out of a html page.</li><li>Moved out of a html document.</li></ol>';

        $text->addHtml($html);
        $text->addHtml($content);

        $this->assertEquals(ChangeText::class, get_class($text));
    }
}
