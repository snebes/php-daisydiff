<?php
/**
 * (c) Steve Nebes <snebes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace DaisyDiff\Html\Ancestor\TagToString;

use DaisyDiff\Html\Ancestor\ChangeText;
use DaisyDiff\Html\Ancestor\TagChangeSemantic;
use DaisyDiff\Html\Dom\TagNode;
use PHPUnit\Framework\TestCase;

/**
 * AnchorToString Tests.
 */
class AnchorToStringTest extends TestCase
{
    public function testAnchor(): void
    {
        $root = new TagNode(null, 'root');
        $intermediate = new TagNode($root, 'middle');
        $root->addChild($intermediate);

        $anchor = new AnchorToString($root, TagChangeSemantic::STYLE);

        $this->assertEquals('!diff-root!', $anchor->getDescription());
    }

    public function testAddAttributes(): void
    {
        $attrs = [
            'class' => 'diff-tag-html',
            'href'  => 'diff-withdestination',
        ];

        $root = new TagNode(null, 'root');
        $intermediate = new TagNode($root, 'middle');
        $root->addChild($intermediate);

        $anchor = new AnchorToString($root, TagChangeSemantic::STYLE);

        $changeText = new ChangeText();
        $newText = '<a href="">Click here</a>';
        $changeText->addText($newText);

        $anchor->addAttributes($changeText, $attrs);

        $this->assertContains('href', $changeText->__toString());
        $this->assertContains('class', $changeText->__toString());
    }
}
