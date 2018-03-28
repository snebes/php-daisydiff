<?php declare(strict_types=1);

namespace DaisyDiff\Html\Ancestor;

use DaisyDiff\Html\Dom\TagNode;
use DaisyDiff\RangeDifferencer\RangeDifference;
use DaisyDiff\RangeDifferencer\RangeDifferencer;
use PHPUnit\Framework\TestCase;

/**
 * ChangeTextGenerator Tests.
 */
class ChangeTextGeneratorTest extends TestCase
{
    public function testGetRangeCount(): void
    {
        $root = new TagNode(null, 'root');
        $intermediate = new TagNode(null, 'middle');

        $firstNodeList = [];
        $firstNodeList[] = $root;
        $firstNodeList[] = $intermediate;

        $html = new TagNode(null, 'html');
        $body = new TagNode(null, 'body');

        $secondNodeList = [];
        $secondNodeList[] = $html;
        $secondNodeList[] = $body;

        $comp  = new AncestorComparator($firstNodeList);
        $other = new AncestorComparator($secondNodeList);
        $textGenerator = new ChangeTextGenerator($comp, $other);

        $this->assertEquals(ChangeTextGenerator::class, get_class($textGenerator));
    }

    public function testGetChanged(): void
    {
        $root = new TagNode(null, 'root');
        $intermediate = new TagNode(null, 'middle');

        $firstNodeList = [];
        $firstNodeList[] = $root;
        $firstNodeList[] = $intermediate;

        $html = new TagNode(null, 'html');
        $body = new TagNode(null, 'body');

        $secondNodeList = [];
        $secondNodeList[] = $html;
        $secondNodeList[] = $body;

        $comp  = new AncestorComparator($firstNodeList);
        $other = new AncestorComparator($secondNodeList);
        $textGenerator = new ChangeTextGenerator($comp, $other);

        $htmlLayoutChanges = [];
        $this->assertEquals($htmlLayoutChanges, $textGenerator->getHtmlLayoutChanges());

        $differences = RangeDifferencer::findDifferences($other, $comp, null);
        $changedText = '<ul class="changelist"><li>Moved out of a <b>html page</b>.</li><li>Moved out of a <b>html document</b>.</li><li><b>!diff-root!</b> added.</li><li><b>!diff-middle!</b> added.</li></ul>';

        $this->assertEquals($changedText, strval($textGenerator->getChanged($differences)));
        $this->assertEquals(ChangeText::class, get_class($textGenerator->getChanged($differences)));
    }
}
