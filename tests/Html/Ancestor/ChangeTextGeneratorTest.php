<?php
/**
 * (c) Steve Nebes <snebes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace DaisyDiff\Html\Ancestor;

use DaisyDiff\Html\Dom\TagNode;
use DaisyDiff\RangeDifferencer\RangeDifferencer;
use PHPUnit\Framework\TestCase;

/**
 * ChangeTextGenerator Tests.
 */
class ChangeTextGeneratorTest extends TestCase
{
    /** @var AncestorComparator */
    private $comp;

    /** @var AncestorComparator */
    private $other;

    /** @var ChangeTextGenerator */
    private $textGenerator;

    protected function setUp()
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

        $this->comp  = new AncestorComparator($firstNodeList);
        $this->other = new AncestorComparator($secondNodeList);

        $this->textGenerator = new ChangeTextGenerator($this->comp, $this->other);
    }

    public function testGetRangeCount(): void
    {
        $this->assertInstanceOf(ChangeTextGenerator::class, $this->textGenerator);
    }

    public function testGetChanged(): void
    {
        $htmlLayoutChanges = [];
        $this->assertSame($htmlLayoutChanges, $this->textGenerator->getHtmlLayoutChanges());

        $differences = RangeDifferencer::findDifferences($this->other, $this->comp);
        $changedText = '<ul class="changelist"><li>Moved out of a <b>html page</b>.</li><li>Moved out of a <b>html document</b>.</li><li><b>!diff-root!</b> added.</li><li><b>!diff-middle!</b> added.</li></ul>';

        $this->assertSame($changedText, strval($this->textGenerator->getChanged($differences)));
        $this->assertInstanceOf(ChangeText::class, $this->textGenerator->getChanged($differences));
    }
}
