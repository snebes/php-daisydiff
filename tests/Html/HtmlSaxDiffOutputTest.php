<?php
/**
 * (c) Steve Nebes <snebes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace SN\DaisyDiff\Html;

use SN\DaisyDiff\Html\Dom\DomTreeBuilder;
use SN\DaisyDiff\Html\Dom\ImageNode;
use SN\DaisyDiff\Html\Dom\TagNode;
use SN\DaisyDiff\Html\Dom\TextNode;
use SN\DaisyDiff\Html\Modification\Modification;
use SN\DaisyDiff\Html\Modification\ModificationType;
use SN\DaisyDiff\Xml\XMLReader;
use PHPUnit\Framework\TestCase;

/**
 * HtmlSaxDiffOutput Tests.
 */
class HtmlSaxDiffOutputTest extends TestCase
{
    /** @var ChangeText */
    private $changeText;

    /** @var HtmlSaxDiffOutput */
    private $htmlDiffOutput;

    protected function setUp()
    {
        $this->changeText = new ChangeText();
        $this->htmlDiffOutput = new HtmlSaxDiffOutput($this->changeText);
    }

    /**
     * @param string $oldText
     * @param string $newText
     * @return string
     */
    private function diff(string $oldText, string $newText): string
    {
        $oldHandler = new DomTreeBuilder();
        $oldReader = new XMLReader($oldHandler);
        $oldReader->parse($oldText);

        $newHandler = new DomTreeBuilder();
        $newReader = new XMLReader($newHandler);
        $newReader->parse($newText);

        // Diff.
        $leftComparator = new TextNodeComparator($oldHandler);
        $rightComparator = new TextNodeComparator($newHandler);

        $differ = new HtmlDiffer($this->htmlDiffOutput);
        $differ->diff($leftComparator, $rightComparator);

        return $this->changeText->getText();
    }

    public function testGenerateOutputExample1(): void
    {
        $html = new TagNode(null, 'html');
        $text1 = new TextNode($html, '[');
        $text2 = new TextNode($html, 'contents of html page');
        $text3 = new TextNode($html, ']');

        $this->htmlDiffOutput->generateOutput($html);

        $expected = '<html>[contents of html page]</html>';

        $this->assertSame($expected, $this->changeText->getText());
    }

    public function testGenerateOutputExample1b(): void
    {
        $oldText = '<p> This is a blue book</p>';
        $newText = '<p> This is a blue book</p>';
        $result = $this->diff($oldText, $newText);

        $this->assertSame($newText, $result);
    }

    public function testGenerateOutputExample2(): void
    {
        $html = new TagNode(null, 'html');
        $text1 = new TextNode($html, '[');
        $text2 = new TextNode($html, 'contents of html page');
        $text3 = new TextNode($html, ']');

        $m = new Modification(ModificationType::ADDED, ModificationType::ADDED);
        $m->setFirstOfId(true);
        $text2->setModification($m);

        $this->htmlDiffOutput->generateOutput($html);

        $expected = '<html>[<ins class="diff-html-added">contents of html page</ins>]</html>';

        $this->assertSame($expected, $this->changeText->getText());
    }

    public function testGenerateOutputExample2b(): void
    {
        $oldText = '<p> This is a blue book</p>';
        $newText = '<p> This is a big blue book</p>';
        $result = $this->diff($oldText, $newText);

        $expected = '<p> This is a <ins class="diff-html-added">big </ins>blue book</p>';

        $this->assertSame($expected, $result);
    }

    public function testGenerateOutputExample3(): void
    {
        $html = new TagNode(null, 'html');
        $text1 = new TextNode($html, '[');
        $text2 = new TextNode($html, 'contents of html page');
        $text3 = new TextNode($html, ']');

        $m = new Modification(ModificationType::CONFLICT, ModificationType::CONFLICT);
        $m->setFirstOfId(true);
        $text2->setModification($m);

        $this->htmlDiffOutput->generateOutput($html);

        $expected = '<html>[<span class="diff-html-conflict">contents of html page</span>]</html>';

        $this->assertSame($expected, $this->changeText->getText());
    }

    public function testGenerateOutputExample4(): void
    {
        $html = new TagNode(null, 'html');
        $text1 = new TextNode($html, '[');
        $text2 = new TextNode($html, 'contents of html page');
        $text3 = new TextNode($html, ']');

        $m = new Modification(ModificationType::REMOVED, ModificationType::REMOVED);
        $m->setFirstOfId(true);
        $text2->setModification($m);

        $this->htmlDiffOutput->generateOutput($html);

        $expected = '<html>[<del class="diff-html-removed">contents of html page</del>]</html>';

        $this->assertSame($expected, $this->changeText->getText());
    }

    public function testGenerateOutputExample5(): void
    {
        $html = new TagNode(null, 'html');
        $text1 = new TextNode($html, '[');
        $text2 = new TextNode($html, 'contents of html page');
        $text3 = new TextNode($html, ']');

        $m = new Modification(ModificationType::CHANGED, ModificationType::CONFLICT);
        $text2->setModification($m);

        $this->htmlDiffOutput->generateOutput($html);

        $expected = '<html>[<span class="diff-html-conflict">contents of html page</span>]</html>';

        $this->assertSame($expected, $this->changeText->getText());
    }

    public function testGenerateOutputExample6(): void
    {
        $html = new TagNode(null, 'html');
        $img = new ImageNode($html, ['src' => 'image.png']);

        $m = new Modification(ModificationType::CHANGED, ModificationType::REMOVED);
        $img->setModification($m);

        $this->htmlDiffOutput->generateOutput($html);

        $expected = '<html><del class="diff-html-removed"><img src="image.png" changeType="diff-removed-image"></img></del></html>';

        $this->assertSame($expected, $this->changeText->getText());
    }

    public function testGenerateOutputExample7(): void
    {
        $html = new TagNode(null, 'html');
        $img = new ImageNode($html, ['src' => 'image.png']);

        $m = new Modification(ModificationType::NONE, ModificationType::NONE);
        $img->setModification($m);

        $this->htmlDiffOutput->generateOutput($html);

        $expected = '<html><img src="image.png"></img></html>';

        $this->assertSame($expected, $this->changeText->getText());
    }

    public function testGenerateOutputExample8(): void
    {
        $html = new TagNode(null, 'html');
        $img = new ImageNode($html, ['src' => 'image.png']);

        $m = new Modification(ModificationType::ADDED, ModificationType::CONFLICT);
        $img->setModification($m);

        $this->htmlDiffOutput->generateOutput($html);

        $expected = '<html><span class="diff-html-conflict"><img src="image.png" changeType="diff-conflict-image"></img></span></html>';

        $this->assertSame($expected, $this->changeText->getText());
    }

    public function testGenerateOutputExample9(): void
    {
        $html = new TagNode(null, 'html');
        $text = new TextNode($html, 'contents of html page');
        $img = new ImageNode($html, ['src' => 'image.png']);

        $previous = new Modification(ModificationType::REMOVED, ModificationType::REMOVED);
        $text->setModification($previous);

        $m = new Modification(ModificationType::ADDED, ModificationType::ADDED);
        $m->setPrevious($previous);
        $img->setModification($m);

        $this->htmlDiffOutput->generateOutput($html);

        $expected = '<html><del class="diff-html-removed">contents of html page</del><ins class="diff-html-added"><img src="image.png" changeType="diff-added-image"></img></ins></html>';

        $this->assertSame($expected, $this->changeText->getText());
    }

    public function testGenerateOutputExample10(): void
    {
        $html = new TagNode(null, 'html');
        $img = new ImageNode($html, ['src' => 'image.png']);
        $text = new TextNode($html, 'contents of html page');

        $previous = new Modification(ModificationType::REMOVED, ModificationType::REMOVED);
        $text->setModification($previous);

        $m = new Modification(ModificationType::ADDED, ModificationType::ADDED);
        $m->setNext($previous);
        $img->setModification($m);

        $this->htmlDiffOutput->generateOutput($html);

        $expected = '<html><ins class="diff-html-added"><img src="image.png" changeType="diff-added-image"></img></ins><del class="diff-html-removed">contents of html page</del></html>';

        $this->assertSame($expected, $this->changeText->getText());
    }

    public function testWriteImageExample1(): void
    {
        $html = new TagNode(null, 'html');
        $img = new ImageNode($html, ['src' => 'image.png']);

        $m = new Modification(ModificationType::ADDED, ModificationType::ADDED);
        $img->setModification($m);

        $this->htmlDiffOutput->generateOutput($html);

        $expected = '<html><ins class="diff-html-added"><img src="image.png" changeType="diff-added-image"></img></ins></html>';

        $this->assertSame($expected, $this->changeText->getText());
    }

    public function testWriteImageExample2(): void
    {
        $html = new TagNode(null, 'html');
        $img = new ImageNode($html, ['src' => 'image.png']);

        $m = new Modification(ModificationType::REMOVED, ModificationType::REMOVED);
        $img->setModification($m);

        $this->htmlDiffOutput->generateOutput($html);

        $expected = '<html><del class="diff-html-removed"><img src="image.png" changeType="diff-removed-image"></img></del></html>';

        $this->assertSame($expected, $this->changeText->getText());
    }

    public function testWriteImageExample3(): void
    {
        $html = new TagNode(null, 'html');
        $img = new ImageNode($html, ['src' => 'image.png']);

        $m = new Modification(ModificationType::CONFLICT, ModificationType::CONFLICT);
        $img->setModification($m);

        $this->htmlDiffOutput->generateOutput($html);

        $expected = '<html><span class="diff-html-conflict"><img src="image.png" changeType="diff-conflict-image"></img></span></html>';

        $this->assertSame($expected, $this->changeText->getText());
    }

    public function testWriteImageExample4(): void
    {
        $html = new TagNode(null, 'html');
        $img = new ImageNode($html, ['src' => 'image.png']);

        $m = new Modification(ModificationType::NONE, ModificationType::NONE);
        $img->setModification($m);

        $this->htmlDiffOutput->generateOutput($html);

        $expected = '<html><img src="image.png"></img></html>';

        $this->assertSame($expected, $this->changeText->getText());
    }
}
