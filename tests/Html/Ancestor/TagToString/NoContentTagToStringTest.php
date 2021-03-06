<?php
/**
 * (c) Steve Nebes <snebes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace SN\DaisyDiff\Html\Ancestor\TagToString;

use SN\DaisyDiff\Html\Ancestor\TagChangeSemantic;
use SN\DaisyDiff\Html\ChangeText;
use SN\DaisyDiff\Html\Dom\TagNode;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;

/**
 * NoContentTagToString Tests.
 */
class NoContentTagToStringTest extends TestCase
{
    public function testGetRemovedDescription(): void
    {
        $div = new TagNode(null, 'div');
        $tagMoved = new NoContentTagToString($div, TagChangeSemantic::MOVED);

        $changeText = new ChangeText();
        $newText = 'Div tag to add styles';
        $changeText->characters($newText);

        $tagMoved->getRemovedDescription($changeText);

        $refProp = new ReflectionProperty($tagMoved, 'sem');
        $refProp->setAccessible(true);

        $this->assertEquals(TagChangeSemantic::MOVED, $refProp->getValue($tagMoved));
        $this->assertEquals('Division', $tagMoved->getDescription());
    }

    public function testGetAddedDescription(): void
    {
        $div = new TagNode(null, 'form');
        $tagMoved = new NoContentTagToString($div, TagChangeSemantic::STYLE);

        $changeText = new ChangeText();
        $newText = 'Form to collect data';
        $changeText->characters($newText);

        $tagMoved->getAddedDescription($changeText);

        $refProp = new ReflectionProperty($tagMoved, 'sem');
        $refProp->setAccessible(true);

        $this->assertEquals(TagChangeSemantic::STYLE, $refProp->getValue($tagMoved));
        $this->assertEquals('Form', $tagMoved->getDescription());
    }
}
