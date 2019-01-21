<?php
/**
 * (c) Steve Nebes <snebes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace DaisyDiff\Html\Dom;

use PHPUnit\Framework\TestCase;

/**
 * DomTreeBuilder Tests.
 */
class DomTreeBuilderTest extends TestCase
{
    public function testStartDocument(): void
    {
        $tree = new DomTreeBuilder();
        $textNodes = [];

        $this->assertEquals('<body>', $tree->getBodyNode()->__toString());
        $this->assertTrue($textNodes === $tree->getTextNodes());
        $this->assertFalse($tree->isDocumentStarted());

        $tree->startDocument();
        $this->assertTrue($tree->isDocumentStarted());
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testStartDocumentException(): void
    {
        $tree = new DomTreeBuilder();
        $tree->startDocument();
        $this->assertTrue($tree->isDocumentStarted());

        $tree->startDocument();
    }

    public function testEndDocument(): void
    {
        $tree = new DomTreeBuilder();
        $textNodes = [];

        $tree->startDocument();
        $this->assertEquals('<body>', $tree->getBodyNode()->__toString());
        $this->assertTrue($textNodes === $tree->getTextNodes());
        $this->assertFalse($tree->isDocumentEnded());

        $tree->endDocument();
        $this->assertTrue($tree->isDocumentEnded());
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testEndDocumentException1(): void
    {
        $tree = new DomTreeBuilder();
        $tree->endDocument();
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testEndDocumentException2(): void
    {
        $tree = new DomTreeBuilder();
        $tree->startDocument();

        $tree->endDocument();
        $tree->endDocument();
    }

    public function testUnitStartElement1(): void
    {
        $tree = new DomTreeBuilder();
        $tree->startDocument();
        $this->setBodyStarted($tree, true);
        $tree->startElement(true, 'p', ['class' => 'test']);
        $this->setBodyEnded($tree, true);
        $tree->endDocument();

        $refProp = new \ReflectionProperty($tree, 'currentParent');
        $refProp->setAccessible(true);
        $element = $refProp->getValue($tree);

        $this->assertTrue($element instanceof TagNode);
        $this->assertSame('<p class="test">', strval($element));
        $this->assertFalse($element->isWhiteBefore());
        $this->assertFalse($element->isWhiteAfter());
    }

    public function testUnitStartElement2(): void
    {
        $tree = new DomTreeBuilder();

        $refProp = new \ReflectionProperty($tree, 'whiteSpaceBeforeThis');
        $refProp->setAccessible(true);
        $refProp->setValue($tree, true);

        $tree->startDocument();
        $this->setBodyStarted($tree, true);
        $tree->startElement(true, 'span', []);
        $this->setBodyEnded($tree, true);
        $tree->endDocument();

        $refProp = new \ReflectionProperty($tree, 'currentParent');
        $refProp->setAccessible(true);
        $element = $refProp->getValue($tree);

        $this->assertTrue($element instanceof TagNode);
        $this->assertSame('<span>', strval($element));
        $this->assertTrue($element->isWhiteBefore());
        $this->assertFalse($element->isWhiteAfter());
    }

    public function testUnitStartElement3(): void
    {
        $tree = new DomTreeBuilder();
        $tree->startDocument();
        $this->setBodyStarted($tree, true);
        $tree->startElement(true, 'pre', []);
        $this->setBodyEnded($tree, true);
        $tree->endDocument();

        $refProp = new \ReflectionProperty($tree, 'numberOfActivePreTags');
        $refProp->setAccessible(true);
        $value = $refProp->getValue($tree);

        $this->assertSame(1, $value);
    }

    public function testUnitStartElement4(): void
    {
        $tree = new DomTreeBuilder();
        $tree->startDocument();
        $tree->startElement(true, 'body', []);

        $refProp = new \ReflectionProperty($tree, 'bodyStarted');
        $refProp->setAccessible(true);
        $value = $refProp->getValue($tree);

        $this->assertTrue($value);
    }

    public function testStartElementExample1(): void
    {
        $attrs = ['class' => 'diff'];

        $tree = new DomTreeBuilder();
        $tree->startDocument();
        $this->setBodyStarted($tree, true);
        $tree->startElement(true, 'p', $attrs);

        $this->assertSame('<p class="diff">', $tree->getBodyNode()->getChild(0)->__toString());
    }

    public function testStartElementExample2(): void
    {
        $attrs = ['class' => 'diff'];

        $tree = new DomTreeBuilder();
        $tree->startDocument();
        $this->setBodyStarted($tree, true);
        $this->setBodyEnded($tree, true);
        $tree->startElement(true, 'p', $attrs);

        $this->assertSame(0, $tree->getBodyNode()->getNumChildren());
    }

    public function testStartElementExample3(): void
    {
        $attrs = ['class' => 'diff'];

        $tree = new DomTreeBuilder();
        $tree->startDocument();
        $this->setBodyStarted($tree, false);
        $this->setBodyEnded($tree, true);
        $tree->startElement(true, 'p', $attrs);

        $this->assertSame(0, $tree->getBodyNode()->getNumChildren());
    }

    public function testStartElementExample4(): void
    {
        $attrs = ['class' => 'diff'];

        $tree = new DomTreeBuilder();
        $tree->startDocument();
        $this->setBodyStarted($tree, false);
        $this->setBodyEnded($tree, false);
        $tree->startElement(true, 'p', $attrs);

        $this->assertSame(0, $tree->getBodyNode()->getNumChildren());
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testStartElementExample5(): void
    {
        $attrs = ['class' => 'diff'];

        $tree = new DomTreeBuilder();
        $this->setBodyStarted($tree, false);
        $this->setBodyEnded($tree, false);

        $tree->startElement(true, 'p', $attrs);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testStartElementExample6(): void
    {
        $attrs = ['class' => 'diff'];

        $tree = new DomTreeBuilder();
        $tree->startDocument();
        $tree->endDocument();

        $this->setBodyStarted($tree, false);
        $this->setBodyEnded($tree, false);

        $tree->startElement(true, 'p', $attrs);
    }

    public function testStartElementExample7(): void
    {
        $attrs = ['class' => 'diff'];

        $tree = new DomTreeBuilder();
        $tree->startDocument();
        $tree->startElement(true, 'body', $attrs);

        $this->assertSame('<body>', $tree->getBodyNode()->__toString());
    }

    public function testStartElementExample8(): void
    {
        $attrs = ['class' => 'diff'];

        $tree = new DomTreeBuilder();
        $tree->startDocument();
        $this->setBodyStarted($tree, true);
        $tree->startElement(true, 'pre', $attrs);

        $this->assertSame('<pre class="diff">', $tree->getBodyNode()->getChild(0)->__toString());
    }

    public function testUnitEndElement1(): void
    {
        $tree = new DomTreeBuilder();
        $tree->startDocument();
        $this->setBodyStarted($tree, true);
        $tree->endElement(true, 'body');

        $refProp = new \ReflectionProperty($tree, 'bodyEnded');
        $refProp->setAccessible(true);
        $value = $refProp->getValue($tree);

        $this->assertTrue($value);
    }

    public function testUnitEndElement2(): void
    {
        $tree = new DomTreeBuilder();

        $refProp = new \ReflectionProperty($tree, 'numberOfActivePreTags');
        $refProp->setAccessible(true);
        $refProp->setValue($tree, 1);

        $tree->startDocument();
        $this->setBodyStarted($tree, true);
        $tree->endElement(true, 'pre');

        $this->assertEquals(0, $refProp->getValue($tree));
    }

    public function testUnitEndElement3(): void
    {
        $tree = new DomTreeBuilder();
        $tree->startDocument();
        $this->setBodyStarted($tree, true);
        $tree->startElement(true, 'span', []);
        $tree->endElement(true, 'img');

        $refProp = new \ReflectionProperty($tree, 'lastSibling');
        $refProp->setAccessible(true);
        $element = $refProp->getValue($tree);

        $this->assertSame('<span>', $element->__toString());
    }

    public function testEndElementExample1(): void
    {
        $attrs = ['class' => 'diff'];

        $tree = new DomTreeBuilder();
        $tree->startDocument();
        $this->setBodyStarted($tree, true);
        $this->setBodyEnded($tree, true);

        $tree->startElement(true, 'p', $attrs);
        $tree->endElement(true, 'p');

        $this->assertSame(0, $tree->getBodyNode()->getNumChildren());
    }

    public function testEndElementExample2(): void
    {
        $attrs = ['class' => 'diff'];

        $tree = new DomTreeBuilder();
        $tree->startDocument();
        $this->setBodyStarted($tree, false);
        $this->setBodyEnded($tree, true);

        $tree->startElement(true, 'p', $attrs);
        $tree->endElement(true, 'p');

        $this->assertSame(0, $tree->getBodyNode()->getNumChildren());
    }

    public function testEndElementExample3(): void
    {
        $attrs = ['class' => 'diff'];

        $tree = new DomTreeBuilder();
        $tree->startDocument();
        $this->setBodyStarted($tree, false);
        $this->setBodyEnded($tree, false);

        $tree->startElement(true, 'p', $attrs);
        $tree->endElement(true, 'p');

        $this->assertSame(0, $tree->getBodyNode()->getNumChildren());
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testEndElementExample4(): void
    {
        $tree = new DomTreeBuilder();
        $this->setBodyStarted($tree, false);
        $this->setBodyEnded($tree, false);

        $tree->endElement(true, 'p');
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testEndElementExample5(): void
    {
        $tree = new DomTreeBuilder();
        $tree->startDocument();
        $tree->endDocument();

        $this->setBodyStarted($tree, false);
        $this->setBodyEnded($tree, false);

        $tree->endElement(true, 'p');
    }

    public function testEndElementExample6(): void
    {
        $attrs = ['class' => 'diff'];

        $tree = new DomTreeBuilder();
        $tree->startDocument();

        $tree->startElement(true, 'body', $attrs);
        $tree->endElement(true, 'body');

        $this->assertSame('<body>', $tree->getBodyNode()->__toString());
    }

    public function testEndElementExample7(): void
    {
        $attrs = [
            'src' => 'http://placehold.it/200x200',
            'alt' => '',
        ];

        $tree = new DomTreeBuilder();
        $tree->startDocument();
        $this->setBodyStarted($tree, true);

        $tree->startElement(true, 'img', $attrs);
        $tree->endElement(true, 'img');

        $this->assertSame('<img src="http://placehold.it/200x200" alt="">', $tree->getBodyNode()->getChild(0)->__toString());
    }

    public function testEndElementExample8(): void
    {
        $attrs = ['class' => 'diff'];

        $tree = new DomTreeBuilder();
        $tree->startDocument();
        $this->setBodyStarted($tree, true);

        $tree->startElement(true, 'pre', $attrs);
        $tree->endElement(true, 'pre');

        $this->assertSame('<pre class="diff">', $tree->getBodyNode()->getChild(0)->__toString());
    }

    public function testCharacters(): void
    {
        $attrs = ['class' => 'diff'];

        $tree = new DomTreeBuilder();
        $tree->startDocument();
        $this->setBodyStarted($tree, true);

        $tree->startElement(true, 'p', $attrs);
        $tree->endElement(true, 'p');

        $c = "/es\t h+ract\nr";
        $tree->characters(true, $c);

        $this->assertSame('<p class="diff">', $tree->getBodyNode()->getChild(0)->__toString());
    }

    public function testUnitCharacters1(): void
    {
        $chars = 'a.a,a"a\'a(a)a?a:a;a!a{a}a-a+a*a=a_a[a]a|';

        $tree = new DomTreeBuilder();
        $tree->startDocument();
        $tree->characters(true, $chars);
        $tree->endDocument();

        $refProp = new \ReflectionProperty($tree, 'textNodes');
        $refProp->setAccessible(true);
        /** @var TextNode[] $textNodes */
        $textNodes = $refProp->getValue($tree);

        $chars = str_split($chars);

        for ($i = 0, $max = count($chars); $i < $max; $i += 2) {
            $this->assertEquals('a', strval($textNodes[$i]));
            $this->assertEquals($chars[$i + 1], strval($textNodes[$i + 1]));

            $this->assertFalse($textNodes[$i]->isWhiteBefore());
            $this->assertFalse($textNodes[$i]->isWhiteAfter());
            $this->assertFalse($textNodes[$i + 1]->isWhiteBefore());
            $this->assertFalse($textNodes[$i + 1]->isWhiteAfter());
        }
    }

    public function testUnitCharacters2(): void
    {
        $chars = 'a a.a,a"a\'a(a)a?a:a;a!a{a}a-a+a*a=a_a[a]a|';
        $tree  = new DomTreeBuilder();

        $refProp = new \ReflectionProperty($tree, 'numberOfActivePreTags');
        $refProp->setAccessible(true);
        $refProp->setValue($tree, 1);

        $tree->startDocument();
        $tree->characters(true, $chars);
        $tree->endDocument();

        $refProp = new \ReflectionProperty($tree, 'textNodes');
        $refProp->setAccessible(true);
        /** @var TextNode[] $textNodes */
        $textNodes = $refProp->getValue($tree);

        $chars = str_split($chars);

        for ($i = 0, $max = count($chars); $i < $max; $i += 2) {
            $this->assertEquals('a', strval($textNodes[$i]));
            $this->assertEquals($chars[$i + 1], strval($textNodes[$i + 1]));

            $this->assertFalse($textNodes[$i]->isWhiteBefore());
            $this->assertFalse($textNodes[$i]->isWhiteAfter());
            $this->assertFalse($textNodes[$i + 1]->isWhiteBefore());
            $this->assertFalse($textNodes[$i + 1]->isWhiteAfter());
        }
    }

    public function testUnitCharacters3(): void
    {
        $chars = 'a/a.a!a,a;a?a=a\'a"a[a]a{a}a(a)a&a|a\\a-a_a+a*a:';

        $tree = new DomTreeBuilder();
        $tree->startDocument();
        $tree->characters(true, $chars);
        $tree->endDocument();

        $refProp = new \ReflectionProperty($tree, 'textNodes');
        $refProp->setAccessible(true);
        /** @var TextNode[] $textNodes */
        $textNodes = $refProp->getValue($tree);

        $chars = str_split($chars);

        for ($i = 0, $max = count($chars); $i < $max; $i += 2) {
            $this->assertEquals('a', strval($textNodes[$i]));
            $this->assertEquals($chars[$i + 1], strval($textNodes[$i + 1]));

            $this->assertFalse($textNodes[$i]->isWhiteBefore());
            $this->assertFalse($textNodes[$i]->isWhiteAfter());
            $this->assertFalse($textNodes[$i + 1]->isWhiteBefore());
            $this->assertFalse($textNodes[$i + 1]->isWhiteAfter());
        }
    }

    public function testUnitCharacters4(): void
    {
        $chars = "a a\na\ra\ta";

        $tree = new DomTreeBuilder();
        $tree->startDocument();
        $tree->characters(true, $chars);
        $tree->endDocument();

        $refProp = new \ReflectionProperty($tree, 'textNodes');
        $refProp->setAccessible(true);
        /** @var TextNode[] $textNodes */
        $textNodes = $refProp->getValue($tree);

        for ($i = 0, $max = 5; $i < $max; $i += 1) {
            $this->assertEquals('a', strval($textNodes[$i]));

            if ($i == 0) {
                $this->assertFalse($textNodes[$i]->isWhiteBefore());
            } else {
                $this->assertTrue($textNodes[$i]->isWhiteBefore());
            }

            if ($i == $max - 1) {
                $this->assertFalse($textNodes[$i]->isWhiteAfter());
            } else {
                $this->assertTrue($textNodes[$i]->isWhiteAfter());
            }
        }
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testCharactersException(): void
    {
        $tree  = new DomTreeBuilder();
        $flags = [
            'documentStarted' => false,
            'documentEnded'   => true,
            'bodyStarted'     => false,
            'bodyEnded'       => true,
        ];

        foreach ($flags as $prop => $value) {
            $refProp = new \ReflectionProperty($tree, $prop);
            $refProp->setAccessible(true);
            $refProp->setValue($tree, $value);
        }

        $c = "/es\t h+ract\nr";
        $tree->characters(true, $c);
    }

    public function testAddSeparatingNode(): void
    {
        $tree = new DomTreeBuilder();
        $tree->startDocument();
        $this->setBodyStarted($tree, true);
        $tree->startElement(true, 'div', []);
        $tree->startElement(true, 'p', []);
        $tree->characters(true, 'test');
        $tree->endElement(true, 'p');
        $tree->endElement(true, 'div');

        $refProp = new \ReflectionProperty($tree, 'textNodes');
        $refProp->setAccessible(true);
        $value = $refProp->getValue($tree);

        $this->assertTrue(end($value) instanceof SeparatingNode);
    }

    /**
     * @param DomTreeBuilder $tree
     * @param bool           $value
     */
    private function setBodyStarted(DomTreeBuilder $tree, bool $value): void
    {
        $refProp = new \ReflectionProperty($tree, 'bodyStarted');
        $refProp->setAccessible(true);
        $refProp->setValue($tree, $value);
    }

    /**
     * @param DomTreeBuilder $tree
     * @param bool           $value
     */
    private function setBodyEnded(DomTreeBuilder $tree, bool $value): void
    {
        $refProp = new \ReflectionProperty($tree, 'bodyEnded');
        $refProp->setAccessible(true);
        $refProp->setValue($tree, $value);
    }
}
