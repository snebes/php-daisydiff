<?php declare(strict_types=1);

namespace DaisyDiff\Html\Ancestor\TagToString;

use DaisyDiff\Html\Ancestor\ChangeText;
use DaisyDiff\Html\Ancestor\TagChangeSemantic;
use DaisyDiff\Html\Dom\TagNode;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;

/**
 * TagToString Tests.
 */
class TagToStringTest extends TestCase
{
    public function testDiffs(): void
    {
        $root = new TagNode(null, 'root');
        $intermediate = new TagNode($root, 'middle');
        $root->addChild($intermediate);

        $tagToString = new TagToString($root, TagChangeSemantic::STYLE);

//        $this->assertEquals('Moved to', $tagToString->getMovedTo());
//        $this->assertEquals('Style added', $tagToString->getStyleAdded());
//        $this->assertEquals('Added', $tagToString->getAdded());
//        $this->assertEquals('Moved out of', $tagToString->getMovedOutOf());
//        $this->assertEquals('Style removed', $tagToString->getStyleRemoved());
//        $this->assertEquals('Removed', $tagToString->getRemoved());
//        $this->assertEquals('And', $tagToString->getAnd());
        $this->assertEquals('!diff-root!', $tagToString->getDescription());
    }

//    public function testGetRemovedDescription(): void
//    {
//        $root = new TagNode(null, 'root');
//        $intermediate = new TagNode($root, 'middle');
//        $root->addChild($intermediate);
//
//        $tagMoved   = new TagToString($root, TagChangeSemantic::MOVED);
//        $tagStyle   = new TagToString($root, TagChangeSemantic::STYLE);
//        $tagUnknown = new TagToString($root, TagChangeSemantic::UNKNOWN);
//
//        $changeText = new ChangeText();
//        $newText    = '<a href="">Click here</a>';
//        $changeText->addText($newText);
//
//        $tagMoved->getRemovedDescription($changeText);
//        $tagStyle->getRemovedDescription($changeText);
//        $tagUnknown->getRemovedDescription($changeText);
//
//        $refProp = new ReflectionProperty($tagMoved, 'sem');
//        $refProp->setAccessible(true);
//
//        $this->assertEquals(TagChangeSemantic::MOVED, $refProp->getValue($tagMoved));
//        $this->assertEquals('<root>', $tagMoved->getHtmlLayoutChange()->getOpeningTag());
//        $this->assertContains('!diff-root', strval($changeText));
//        $this->assertContains('moved', strval($changeText));
//        $this->assertContains('style', strval($changeText));
//        $this->assertContains('removed', strval($changeText));
//    }
//
//    public function testGetAddedDescription(): void
//    {
//        $root = new TagNode(null, 'html');
//        $intermediate = new TagNode($root, 'body');
//        $root->addChild($intermediate);
//
//        $tagMoved   = new TagToString($root, TagChangeSemantic::MOVED);
//        $tagStyle   = new TagToString($root, TagChangeSemantic::STYLE);
//        $tagUnknown = new TagToString($root, TagChangeSemantic::UNKNOWN);
//
//        $changeText = new ChangeText();
//        $newText    = '<a href="">Click here</a>';
//        $changeText->addText($newText);
//
//        $tagMoved->getAddedDescription($changeText);
//        $tagStyle->getAddedDescription($changeText);
//        $tagUnknown->getAddedDescription($changeText);
//
//        $refProp = new ReflectionProperty($tagMoved, 'sem');
//        $refProp->setAccessible(true);
//
//        $this->assertEquals(TagChangeSemantic::MOVED, $refProp->getValue($tagMoved));
//        $this->assertEquals('</html>', $tagMoved->getHtmlLayoutChange()->getEndingTag());
//        $this->assertContains('Moved', strval($changeText));
//        $this->assertContains('style', strval($changeText));
//        $this->assertContains('added', strval($changeText));
//    }
//
//    public function testAddAttributes(): void
//    {
//        $attrs = [
//            'src'    => 'source',
//            'width'  => 'width',
//            'height' => 'height',
//            'class'  => 'height',
//        ];
//
//        $root = new TagNode(null, 'root');
//        $intermediate = new TagNode($root, 'middle');
//        $root->addChild($intermediate);
//
//        $tagMoved = new TagToString($root, TagChangeSemantic::MOVED);
//
//        $changeText = new ChangeText();
//        $newText    = '<a href="">Click here</a>';
//        $changeText->addText($newText);
//
//        $tagMoved->addAttributes($changeText, $attrs);
//
//        $refProp = new ReflectionProperty($tagMoved, 'node');
//        $refProp->setAccessible(true);
//
//        $this->assertEquals($root, $refProp->getValue($tagMoved));
//        $this->assertContains('source', strval($changeText));
//        $this->assertContains('height', strval($changeText));
//        $this->assertContains('width', strval($changeText));
//    }
}
