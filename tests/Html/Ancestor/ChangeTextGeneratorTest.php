<?php
/**
 * (c) Steve Nebes <snebes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace SN\DaisyDiff\Html\Ancestor;

use SN\DaisyDiff\Html\ChangeText;
use SN\DaisyDiff\Html\Dom\TagNode;
use SN\DaisyDiff\RangeDifferencer\RangeDifferencer;
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

    protected function setUp(): void
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

        $this->comp = new AncestorComparator($firstNodeList);
        $this->other = new AncestorComparator($secondNodeList);
    }

    public function testChangeTextGenerator(): void
    {
        $textGenerator = new ChangeTextGenerator($this->comp, $this->other);

        $this->assertInstanceOf(ChangeTextGenerator::class, $textGenerator);
    }

    public function testGetChanged(): void
    {
        $textGenerator = new ChangeTextGenerator($this->comp, $this->other);

        $changes = [];
        $this->assertSame($changes, $textGenerator->getHtmlLayoutChanges());

        $differences = RangeDifferencer::findDifferences($this->other, $this->comp);
        $changed = $textGenerator->getChanged($differences);
        $changedText = <<<HTML
<ul class="changelist"><li>Moved out of a <b>html page</b>.</li><li>Moved out of a <b>html document</b>.</li><li><b>!diff-root!</b> added.</li><li><b>!diff-middle!</b> added.</li></ul>
HTML;


        $this->assertSame($changedText, $changed->getText());
        $this->assertInstanceOf(ChangeText::class, $changed);
    }
}
