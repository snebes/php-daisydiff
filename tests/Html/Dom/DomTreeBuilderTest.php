<?php declare(strict_types=1);

namespace DaisyDiff\Html\Dom;

use PHPUnit\Framework\TestCase;
use ReflectionProperty;
use RuntimeException;

/**
 * DomTreeBuilder Tests.
 *
 * @covers DaisyDiff\Html\Dom\DomTreeBuilder::__construct
 * @covers DaisyDiff\Html\Dom\DomTreeBuilder::endWord
 * @covers DaisyDiff\Html\Dom\DomTreeBuilder::isSeparatingTag
 * @covers DaisyDiff\Html\Dom\DomTreeBuilder::addSeparatorNode
 * @covers DaisyDiff\Html\Dom\DomTreeBuilder::isDelimiter
 */
class DomTreeBuilderTest extends TestCase
{
    /**
     * @covers DaisyDiff\Html\Dom\DomTreeBuilder::isDocumentStarted
     * @covers DaisyDiff\Html\Dom\DomTreeBuilder::startDocument
     * @covers DaisyDiff\Html\Dom\DomTreeBuilder::getBodyNode
     * @covers DaisyDiff\Html\Dom\DomTreeBuilder::getTextNodes
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
     * @covers DaisyDiff\Html\Dom\DomTreeBuilder::startDocument
     * @expectedException RuntimeException
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
     * @covers DaisyDiff\Html\Dom\DomTreeBuilder::isDocumentEnded
     * @covers DaisyDiff\Html\Dom\DomTreeBuilder::endDocument
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
     * @covers DaisyDiff\Html\Dom\DomTreeBuilder::endDocument
     * @expectedException RuntimeException
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
     * @covers DaisyDiff\Html\Dom\DomTreeBuilder::endDocument
     * @expectedException RuntimeException
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
     * @covers DaisyDiff\Html\Dom\DomTreeBuilder::startElement
     */
    public function testStartElementExample1(): void
    {
        $attrs = ['class' => 'diff'];

        $tree = new DomTreeBuilder();
        $tree->startDocument();
        $this->setBodyStarted($tree, true);
        $tree->startElement(null, 'p', $attrs);

        $this->assertEquals('<p class="diff">', strval($tree->getBodyNode()->getChild(0)));
    }

    /**
     * @covers DaisyDiff\Html\Dom\DomTreeBuilder::startElement
     */
    public function testStartElementExample2(): void
    {
        $attrs = ['class' => 'diff'];

        $tree = new DomTreeBuilder();
        $tree->startDocument();
        $this->setBodyStarted($tree, true);
        $this->setBodyEnded($tree, true);
        $tree->startElement(null, 'p', $attrs);

        $this->assertEquals(0, strval($tree->getBodyNode()->getNumChildren()));
    }

    /**
     * @covers DaisyDiff\Html\Dom\DomTreeBuilder::startElement
     */
    public function testStartElementExample3(): void
    {
        $attrs = ['class' => 'diff'];

        $tree = new DomTreeBuilder();
        $tree->startDocument();
        $this->setBodyStarted($tree, false);
        $this->setBodyEnded($tree, true);
        $tree->startElement(null, 'p', $attrs);

        $this->assertEquals(0, strval($tree->getBodyNode()->getNumChildren()));
    }

    /**
     * @covers DaisyDiff\Html\Dom\DomTreeBuilder::startElement
     */
    public function testStartElementExample4(): void
    {
        $attrs = ['class' => 'diff'];

        $tree = new DomTreeBuilder();
        $tree->startDocument();
        $this->setBodyStarted($tree, false);
        $this->setBodyEnded($tree, false);
        $tree->startElement(null, 'p', $attrs);

        $this->assertEquals(0, strval($tree->getBodyNode()->getNumChildren()));
    }

    /**
     * @covers DaisyDiff\Html\Dom\DomTreeBuilder::startElement
     * @expectedException RuntimeException
     */
    public function testStartElementExample5(): void
    {
        $attrs = ['class' => 'diff'];

        $tree = new DomTreeBuilder();
        $this->setBodyStarted($tree, false);
        $this->setBodyEnded($tree, false);

        try {
            $tree->startElement(null, 'p', $attrs);
        } catch (RuntimeException $e) {
            $this->assertEquals(8002, $e->getCode());
            throw $e;
        }
    }

    /**
     * @covers DaisyDiff\Html\Dom\DomTreeBuilder::startElement
     * @expectedException RuntimeException
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
            $tree->startElement(null, 'p', $attrs);
        } catch (RuntimeException $e) {
            $this->assertEquals(8002, $e->getCode());
            throw $e;
        }
    }

    /**
     * @covers DaisyDiff\Html\Dom\DomTreeBuilder::startElement
     */
    public function testStartElementExample7(): void
    {
        $attrs = ['class' => 'diff'];

        $tree = new DomTreeBuilder();
        $tree->startDocument();

        $tree->startElement(null, 'body', $attrs);
        $this->assertEquals('<body>', strval($tree->getBodyNode()));
    }

    /**
     * @covers DaisyDiff\Html\Dom\DomTreeBuilder::startElement
     */
    public function testStartElementExample8(): void
    {
        $attrs = ['class' => 'diff'];

        $tree = new DomTreeBuilder();
        $tree->startDocument();
        $this->setBodyStarted($tree, true);

        $tree->startElement(null, 'pre', $attrs);
        $this->assertEquals('<pre class="diff">', strval($tree->getBodyNode()->getChild(0)));
    }

    /**
     * @covers DaisyDiff\Html\Dom\DomTreeBuilder::endElement
     */
    public function testEndElementExample1(): void
    {
        $attrs = ['class' => 'diff'];

        $tree = new DomTreeBuilder();
        $tree->startDocument();
        $this->setBodyStarted($tree, true);
        $this->setBodyEnded($tree, true);

        $tree->startElement(null, 'p', $attrs);
        $tree->endElement(null, 'p');

        $this->assertEquals(0, $tree->getBodyNode()->getNumChildren());
    }

    /**
     * @covers DaisyDiff\Html\Dom\DomTreeBuilder::endElement
     */
    public function testEndElementExample2(): void
    {
        $attrs = ['class' => 'diff'];

        $tree = new DomTreeBuilder();
        $tree->startDocument();
        $this->setBodyStarted($tree, false);
        $this->setBodyEnded($tree, true);

        $tree->startElement(null, 'p', $attrs);
        $tree->endElement(null, 'p');

        $this->assertEquals(0, $tree->getBodyNode()->getNumChildren());
    }

    /**
     * @covers DaisyDiff\Html\Dom\DomTreeBuilder::endElement
     */
    public function testEndElementExample3(): void
    {
        $attrs = ['class' => 'diff'];

        $tree = new DomTreeBuilder();
        $tree->startDocument();
        $this->setBodyStarted($tree, false);
        $this->setBodyEnded($tree, false);

        $tree->startElement(null, 'p', $attrs);
        $tree->endElement(null, 'p');

        $this->assertEquals(0, $tree->getBodyNode()->getNumChildren());
    }

    /**
     * @covers DaisyDiff\Html\Dom\DomTreeBuilder::endElement
     * @expectedException RuntimeException
     */
    public function testEndElementExample4(): void
    {
        $attrs = ['class' => 'diff'];

        $tree = new DomTreeBuilder();
        $this->setBodyStarted($tree, false);
        $this->setBodyEnded($tree, false);

        try {
            $tree->endElement(null, 'p');
        } catch (RuntimeException $e) {
            $this->assertEquals(8003, $e->getCode());
            throw $e;
        }
    }

    /**
     * @covers DaisyDiff\Html\Dom\DomTreeBuilder::endElement
     * @expectedException RuntimeException
     */
    public function testEndElementExample5(): void
    {
        $attrs = ['class' => 'diff'];

        $tree = new DomTreeBuilder();
        $tree->startDocument();
        $tree->endDocument();

        $this->setBodyStarted($tree, false);
        $this->setBodyEnded($tree, false);

        try {
            $tree->endElement(null, 'p');
        } catch (RuntimeException $e) {
            $this->assertEquals(8003, $e->getCode());
            throw $e;
        }
    }

    /**
     * @covers DaisyDiff\Html\Dom\DomTreeBuilder::endElement
     */
    public function testEndElementExample6(): void
    {
        $attrs = ['class' => 'diff'];

        $tree = new DomTreeBuilder();
        $tree->startDocument();

        $tree->startElement(null, 'body', $attrs);
        $tree->endElement(null, 'body');

        $this->assertEquals('<body>', strval($tree->getBodyNode()));
    }

    /**
     * @covers DaisyDiff\Html\Dom\DomTreeBuilder::endElement
     */
    public function testEndElementExample7(): void
    {
        $attrs = ['src' => 'Image path'];

        $tree = new DomTreeBuilder();
        $tree->startDocument();
        $this->setBodyStarted($tree, true);

        $tree->startElement(null, 'img', $attrs);
        $tree->endElement(null, 'img');

        $this->assertEquals('<img src="Image path">', strval($tree->getBodyNode()->getChild(0)));
    }

    /**
     * @covers DaisyDiff\Html\Dom\DomTreeBuilder::endElement
     */
    public function testEndElementExample8(): void
    {
        $attrs = ['class' => 'diff'];

        $tree = new DomTreeBuilder();
        $tree->startDocument();
        $this->setBodyStarted($tree, true);

        $tree->startElement(null, 'pre', $attrs);
        $tree->endElement(null, 'pre');

        $this->assertEquals('<pre class="diff">', strval($tree->getBodyNode()->getChild(0)));
    }

    /**
     * @covers DaisyDiff\Html\Dom\DomTreeBuilder::characters
     */
    public function testCharacters(): void
    {
        $attrs = ['class' => 'diff'];

        $tree = new DomTreeBuilder();
        $tree->startDocument();
        $this->setBodyStarted($tree, true);

        $tree->startElement(null, 'p', $attrs);
        $tree->endElement(null, 'p');

        $c = "/es\t h+ract\nr";
        $tree->characters(null, $c);

        $this->assertEquals('<p class="diff">', strval($tree->getBodyNode()->getChild(0)));
    }

    /**
     * @covers DaisyDiff\Html\Dom\DomTreeBuilder::characters
     * @expectedException RuntimeException
     */
    public function testCharactersException(): void
    {
        $attrs = ['class' => 'diff'];

        $tree = new DomTreeBuilder();
        $flags = [
            'documentStarted' => false,
            'documentEnded'   => true,
            'bodyStarted'     => false,
            'bodyEnded'       => true,
        ];

        foreach ($flags as $prop => $value) {
            $refProp = new ReflectionProperty($tree, $prop);
            $refProp->setAccessible(true);
            $refProp->setValue($tree, $value);
        }

        try {
            $c = "/es\t h+ract\nr";
            $tree->characters(null, $c);
        } catch (RuntimeException $e) {
            $this->assertEquals(8004, $e->getCode());
            throw $e;
        }
    }

    /**
     * @param  DomTreeBuilder $tree
     * @param  bool           $value
     */
    private function setBodyStarted(DomTreeBuilder $tree, bool $value): void
    {
        $refProp = new ReflectionProperty($tree, 'bodyStarted');
        $refProp->setAccessible(true);
        $refProp->setValue($tree, $value);
    }

    /**
     * @param  DomTreeBuilder $tree
     * @param  bool           $value
     */
    private function setBodyEnded(DomTreeBuilder $tree, bool $value): void
    {
        $refProp = new ReflectionProperty($tree, 'bodyEnded');
        $refProp->setAccessible(true);
        $refProp->setValue($tree, $value);
    }
}
