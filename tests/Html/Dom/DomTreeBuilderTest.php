<?php declare(strict_types=1);

namespace DaisyDiff\Html\Dom;

use PHPUnit\Framework\TestCase;
use ReflectionProperty;
use RuntimeException;

/**
 * DomTreeBuilder Tests.
 */
class DomTreeBuilderTest extends TestCase
{
    /**
     * @group unit
     */
    public function testStartDocument(): void
    {
        $tree = new DomTreeBuilder();
        $textNodes = [];

        $this->assertEquals('<body>', strval($tree->getBodyNode()));
        $this->assertEquals($textNodes, $tree->getTextNodes());
        $this->assertFalse($tree->isDocumentStarted());

        $tree->startDocument();
        $this->assertTrue($tree->isDocumentStarted());
    }

    /**
     * @expectedException RuntimeException
     * @group unit
     */
    public function testStartDocumentException(): void
    {
        $tree = new DomTreeBuilder();
        $tree->startDocument();
        $this->assertTrue($tree->isDocumentStarted());

        try {
            $tree->startDocument();
        } catch (RuntimeException $e) {
            $this->assertEquals(8000, $e->getCode());
            throw $e;
        }
    }

    /**
     * @group unit
     */
    public function testEndDocument(): void
    {
        $tree = new DomTreeBuilder();
        $textNodes = [];

        $tree->startDocument();
        $this->assertEquals('<body>', strval($tree->getBodyNode()));
        $this->assertEquals($textNodes, $tree->getTextNodes());
        $this->assertFalse($tree->isDocumentEnded());

        $tree->endDocument();
        $this->assertTrue($tree->isDocumentEnded());
    }

    /**
     * @expectedException RuntimeException
     * @group unit
     */
    public function testEndDocumentException1(): void
    {
        $tree = new DomTreeBuilder();

        try {
            $tree->endDocument();
        } catch (RuntimeException $e) {
            $this->assertEquals(8001, $e->getCode());
            throw $e;
        }
    }

    /**
     * @expectedException RuntimeException
     * @group unit
     */
    public function testEndDocumentException2(): void
    {
        $tree = new DomTreeBuilder();
        $tree->startDocument();
        $tree->endDocument();

        try {
            $tree->endDocument();
        } catch (RuntimeException $e) {
            $this->assertEquals(8001, $e->getCode());
            throw $e;
        }
    }

    /**
     * @group unit
     */
    public function testUnitStartElement1(): void
    {
        $tree = new DomTreeBuilder();
        $tree->startDocument();
        $this->setBodyStarted($tree, true);
        $tree->startElement(true, 'p', ['class' => 'test']);
        $this->setBodyEnded($tree, true);
        $tree->endDocument();

        try {
            $refProp = new ReflectionProperty($tree, 'currentParent');
            $refProp->setAccessible(true);
            $element = $refProp->getValue($tree);

            $this->assertTrue($element instanceof TagNode);
            $this->assertEquals('<p class="test">', strval($element));
            $this->assertFalse($element->isWhiteBefore());
            $this->assertFalse($element->isWhiteAfter());
        } catch (\ReflectionException $e) {}
    }

    /**
     * @group unit
     */
    public function testUnitStartElement2(): void
    {
        $tree = new DomTreeBuilder();

        try {
            $refProp = new ReflectionProperty($tree, 'whiteSpaceBeforeThis');
            $refProp->setAccessible(true);
            $refProp->setValue($tree, true);
        } catch (\ReflectionException $e) {}

        $tree->startDocument();
        $this->setBodyStarted($tree, true);
        $tree->startElement(true, 'span', []);
        $this->setBodyEnded($tree, true);
        $tree->endDocument();

        try {
            $refProp = new ReflectionProperty($tree, 'currentParent');
            $refProp->setAccessible(true);
            $element = $refProp->getValue($tree);

            $this->assertTrue($element instanceof TagNode);
            $this->assertEquals('<span>', strval($element));
            $this->assertTrue($element->isWhiteBefore());
            $this->assertFalse($element->isWhiteAfter());
        } catch (\ReflectionException $e) {}
    }

    /**
     * @group unit
     */
    public function testUnitStartElement3(): void
    {
        $tree = new DomTreeBuilder();
        $tree->startDocument();
        $this->setBodyStarted($tree, true);
        $tree->startElement(true, 'pre', []);
        $this->setBodyEnded($tree, true);
        $tree->endDocument();

        try {
            $refProp = new ReflectionProperty($tree, 'numberOfActivePreTags');
            $refProp->setAccessible(true);
            $value = $refProp->getValue($tree);

            $this->assertEquals(1, $value);
        } catch (\ReflectionException $e) {}
    }

    /**
     * @group unit
     */
    public function testUnitStartElement4(): void
    {
        $tree = new DomTreeBuilder();
        $tree->startDocument();
        $tree->startElement(true, 'body', []);

        try {
            $refProp = new ReflectionProperty($tree, 'bodyStarted');
            $refProp->setAccessible(true);
            $value = $refProp->getValue($tree);

            $this->assertTrue($value);
        } catch (\ReflectionException $e) {}
    }

    public function testStartElementExample1(): void
    {
        $attrs = ['class' => 'diff'];

        $tree = new DomTreeBuilder();
        $tree->startDocument();
        $this->setBodyStarted($tree, true);
        $tree->startElement(true, 'p', $attrs);

        $this->assertEquals('<p class="diff">', strval($tree->getBodyNode()->getChild(0)));
    }

    public function testStartElementExample2(): void
    {
        $attrs = ['class' => 'diff'];

        $tree = new DomTreeBuilder();
        $tree->startDocument();
        $this->setBodyStarted($tree, true);
        $this->setBodyEnded($tree, true);
        $tree->startElement(true, 'p', $attrs);

        $this->assertEquals(0, strval($tree->getBodyNode()->getNumChildren()));
    }

    public function testStartElementExample3(): void
    {
        $attrs = ['class' => 'diff'];

        $tree = new DomTreeBuilder();
        $tree->startDocument();
        $this->setBodyStarted($tree, false);
        $this->setBodyEnded($tree, true);
        $tree->startElement(true, 'p', $attrs);

        $this->assertEquals(0, strval($tree->getBodyNode()->getNumChildren()));
    }

    public function testStartElementExample4(): void
    {
        $attrs = ['class' => 'diff'];

        $tree = new DomTreeBuilder();
        $tree->startDocument();
        $this->setBodyStarted($tree, false);
        $this->setBodyEnded($tree, false);
        $tree->startElement(true, 'p', $attrs);

        $this->assertEquals(0, strval($tree->getBodyNode()->getNumChildren()));
    }

    /**
     * @expectedException RuntimeException
     * @group unit
     */
    public function testStartElementExample5(): void
    {
        $attrs = ['class' => 'diff'];

        $tree = new DomTreeBuilder();
        $this->setBodyStarted($tree, false);
        $this->setBodyEnded($tree, false);

        try {
            $tree->startElement(true, 'p', $attrs);
        } catch (RuntimeException $e) {
            $this->assertEquals(8002, $e->getCode());
            throw $e;
        }
    }

    /**
     * @expectedException RuntimeException
     * @group unit
     */
    public function testStartElementExample6(): void
    {
        $attrs = ['class' => 'diff'];

        $tree = new DomTreeBuilder();
        $tree->startDocument();
        $tree->endDocument();

        $this->setBodyStarted($tree, false);
        $this->setBodyEnded($tree, false);

        try {
            $tree->startElement(true, 'p', $attrs);
        } catch (RuntimeException $e) {
            $this->assertEquals(8002, $e->getCode());
            throw $e;
        }
    }

    public function testStartElementExample7(): void
    {
        $attrs = ['class' => 'diff'];

        $tree = new DomTreeBuilder();
        $tree->startDocument();

        $tree->startElement(true, 'body', $attrs);
        $this->assertEquals('<body>', strval($tree->getBodyNode()));
    }

    public function testStartElementExample8(): void
    {
        $attrs = ['class' => 'diff'];

        $tree = new DomTreeBuilder();
        $tree->startDocument();
        $this->setBodyStarted($tree, true);

        $tree->startElement(true, 'pre', $attrs);
        $this->assertEquals('<pre class="diff">', strval($tree->getBodyNode()->getChild(0)));
    }

    /**
     * @group unit
     */
    public function testUnitEndElement1(): void
    {
        $tree = new DomTreeBuilder();
        $tree->startDocument();
        $this->setBodyStarted($tree, true);
        $tree->endElement(true, 'body');

        try {
            $refProp = new ReflectionProperty($tree, 'bodyEnded');
            $refProp->setAccessible(true);
            $value = $refProp->getValue($tree);

            $this->assertTrue($value);
        } catch (\ReflectionException $e) {}
    }

    /**
     * @group unit
     */
    public function testUnitEndElement2(): void
    {
        $tree = new DomTreeBuilder();

        try {
            $refProp = new ReflectionProperty($tree, 'numberOfActivePreTags');
            $refProp->setAccessible(true);
            $refProp->setValue($tree, 1);

            $tree->startDocument();
            $this->setBodyStarted($tree, true);
            $tree->endElement(true, 'pre');

            $this->assertEquals(0, $refProp->getValue($tree));
        } catch (\ReflectionException $e) {}
    }

    /**
     * @group unit
     */
    public function testUnitEndElement3(): void
    {
        $tree = new DomTreeBuilder();
        $tree->startDocument();
        $this->setBodyStarted($tree, true);
        $tree->startElement(true, 'span', []);
        $tree->endElement(true, 'img');

        try {
            $refProp = new ReflectionProperty($tree, 'lastSibling');
            $refProp->setAccessible(true);
            $element = $refProp->getValue($tree);

            $this->assertEquals('<span>', strval($element));
        } catch (\ReflectionException $e) {}
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

        $this->assertEquals(0, $tree->getBodyNode()->getNumChildren());
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

        $this->assertEquals(0, $tree->getBodyNode()->getNumChildren());
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

        $this->assertEquals(0, $tree->getBodyNode()->getNumChildren());
    }

    /**
     * @expectedException RuntimeException
     * @group unit
     */
    public function testEndElementExample4(): void
    {
        $tree = new DomTreeBuilder();
        $this->setBodyStarted($tree, false);
        $this->setBodyEnded($tree, false);

        try {
            $tree->endElement(true, 'p');
        } catch (RuntimeException $e) {
            $this->assertEquals(8003, $e->getCode());
            throw $e;
        }
    }

    /**
     * @expectedException RuntimeException
     * @group unit
     */
    public function testEndElementExample5(): void
    {
        $tree = new DomTreeBuilder();
        $tree->startDocument();
        $tree->endDocument();

        $this->setBodyStarted($tree, false);
        $this->setBodyEnded($tree, false);

        try {
            $tree->endElement(true, 'p');
        } catch (RuntimeException $e) {
            $this->assertEquals(8003, $e->getCode());
            throw $e;
        }
    }

    public function testEndElementExample6(): void
    {
        $attrs = ['class' => 'diff'];

        $tree = new DomTreeBuilder();
        $tree->startDocument();

        $tree->startElement(true, 'body', $attrs);
        $tree->endElement(true, 'body');

        $this->assertEquals('<body>', strval($tree->getBodyNode()));
    }

    public function testEndElementExample7(): void
    {
        $attrs = ['src' => 'http://placehold.it/200x200'];

        $tree = new DomTreeBuilder();
        $tree->startDocument();
        $this->setBodyStarted($tree, true);

        $tree->startElement(true, 'img', $attrs);
        $tree->endElement(true, 'img');

        $this->assertEquals('<img src="http://placehold.it/200x200">', strval($tree->getBodyNode()->getChild(0)));
    }

    public function testEndElementExample8(): void
    {
        $attrs = ['class' => 'diff'];

        $tree = new DomTreeBuilder();
        $tree->startDocument();
        $this->setBodyStarted($tree, true);

        $tree->startElement(true, 'pre', $attrs);
        $tree->endElement(true, 'pre');

        $this->assertEquals('<pre class="diff">', strval($tree->getBodyNode()->getChild(0)));
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

        $this->assertEquals('<p class="diff">', strval($tree->getBodyNode()->getChild(0)));
    }

    /**
     * @group unit
     */
    public function testUnitCharacters1(): void
    {
        $chars = 'a.a,a"a\'a(a)a?a:a;a!a{a}a-a+a*a=a_a[a]a|';

        $tree = new DomTreeBuilder();
        $tree->startDocument();
        $tree->characters(true, $chars);
        $tree->endDocument();

        try {
            $refProp = new ReflectionProperty($tree, 'textNodes');
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
        } catch (\ReflectionException $e) {}
    }

    /**
     * @group unit
     */
    public function testUnitCharacters2(): void
    {
        $chars = 'a a.a,a"a\'a(a)a?a:a;a!a{a}a-a+a*a=a_a[a]a|';
        $tree = new DomTreeBuilder();

        try {
            $refProp = new ReflectionProperty($tree, 'numberOfActivePreTags');
            $refProp->setAccessible(true);
            $refProp->setValue($tree, 1);
        } catch (\ReflectionException $e) {}

        $tree->startDocument();
        $tree->characters(true, $chars);
        $tree->endDocument();

        try {
            $refProp = new ReflectionProperty($tree, 'textNodes');
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
        } catch (\ReflectionException $e) {}
    }

    /**
     * @group unit
     */
    public function testUnitCharacters3(): void
    {
        $chars = 'a/a.a!a,a;a?a=a\'a"a[a]a{a}a(a)a&a|a\\a-a_a+a*a:';

        $tree = new DomTreeBuilder();
        $tree->startDocument();
        $tree->characters(true, $chars);
        $tree->endDocument();

        try {
            $refProp = new ReflectionProperty($tree, 'textNodes');
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
        } catch (\ReflectionException $e) {}
    }

    /**
     * @group unit
     */
    public function testUnitCharacters4(): void
    {
        $chars = "a a\na\ra\ta";

        $tree = new DomTreeBuilder();
        $tree->startDocument();
        $tree->characters(true, $chars);
        $tree->endDocument();

        try {
            $refProp = new ReflectionProperty($tree, 'textNodes');
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
        } catch (\ReflectionException $e) {}
    }

    /**
     * @expectedException RuntimeException
     * @group unit
     */
    public function testCharactersException(): void
    {
        $tree = new DomTreeBuilder();
        $flags = [
            'documentStarted' => false,
            'documentEnded'   => true,
            'bodyStarted'     => false,
            'bodyEnded'       => true,
        ];

        foreach ($flags as $prop => $value) {
            try {
                $refProp = new ReflectionProperty($tree, $prop);
                $refProp->setAccessible(true);
                $refProp->setValue($tree, $value);
            } catch (\ReflectionException $e) {}
        }

        try {
            $c = "/es\t h+ract\nr";
            $tree->characters(true, $c);
        } catch (RuntimeException $e) {
            $this->assertEquals(8004, $e->getCode());
            throw $e;
        }
    }

    /**
     * @group unit
     */
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

        try {
            $refProp = new ReflectionProperty($tree, 'textNodes');
            $refProp->setAccessible(true);
            $value = $refProp->getValue($tree);
        } catch (\ReflectionException $e) {}

        $this->assertTrue(end($value) instanceof SeparatingNode);
    }

    /**
     * @param  DomTreeBuilder $tree
     * @param  bool           $value
     */
    private function setBodyStarted(DomTreeBuilder $tree, bool $value): void
    {
        try {
            $refProp = new ReflectionProperty($tree, 'bodyStarted');
            $refProp->setAccessible(true);
            $refProp->setValue($tree, $value);
        } catch (\ReflectionException $e) {}
    }

    /**
     * @param  DomTreeBuilder $tree
     * @param  bool           $value
     */
    private function setBodyEnded(DomTreeBuilder $tree, bool $value): void
    {
        try {
            $refProp = new ReflectionProperty($tree, 'bodyEnded');
            $refProp->setAccessible(true);
            $refProp->setValue($tree, $value);
        } catch (\ReflectionException $e) {}
    }
}
