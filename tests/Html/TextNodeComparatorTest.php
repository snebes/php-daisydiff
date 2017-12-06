<?php declare(strict_types=1);

namespace DaisyDiff\Html;

use DaisyDiff\Html\Dom\DomTreeBuilder;
use DaisyDiff\Html\Dom\TagNode;
use DaisyDiff\Html\Dom\TextNode;
use DaisyDiff\Html\Modification\Modification;
use DaisyDiff\Html\Modification\ModificationType;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;

/**
 * TextNodeComparator Tests.
 */
class TextNodeComparatorTest extends TestCase
{
    /**
     * @param  TextNodeComparator $comp
     * @return TextNode[]
     */
    private function getTextNodes(TextNodeComparator $comp): iterable
    {
        $p = new TagNode(null, 'p');
        $text = new TextNode($p, 'contents of p node');

        $b = new TagNode(null, 'b');
        $boldText = new TextNode($b, 'contents of bold node');

        $refProp = new ReflectionProperty($comp, 'textNodes');
        $refProp->setAccessible(true);

        $textNodes = $refProp->getValue($comp);
        $textNodes[] = $text;
        $textNodes[] = $boldText;
        $refProp->setValue($comp, $textNodes);

        return $textNodes;
    }

    public function testTextNodeComparator(): void
    {
        $p = new TagNode(null, 'p');
        $text = new TextNode($p, 'contents of p node');

        $tree = new DomTreeBuilder();
        $comp = new TextNodeComparator($tree);

        $refProp = new ReflectionProperty($comp, 'textNodes');
        $refProp->setAccessible(true);

        $textNodes = $refProp->getValue($comp);
        $textNodes[] = $text;
        $refProp->setValue($comp, $textNodes);

        $this->assertEquals('<body>', strval($comp->getBodyNode()));
        $this->assertEquals('contents of p node', strval($comp->getTextNode(0)));
        $this->assertEquals(1, $comp->getRangeCount());
    }

    public function testMarkAsNewExample1(): void
    {
        $tree = new DomTreeBuilder();
        $comp = new TextNodeComparator($tree);
        $textNodes = $this->getTextNodes($comp);

        $comp->markAsNew(0, 1);

        $lastModified = $comp->getLastModified();
        $this->assertEquals('added', strval($lastModified[0]->getOutputType()));
    }

    public function testMarkAsNewExample2(): void
    {
        $tree = new DomTreeBuilder();
        $comp = new TextNodeComparator($tree);
        $textNodes = $this->getTextNodes($comp);

        $comp->markAsNew(1, 0);

        $lastModified = $comp->getLastModified();
        $this->assertEquals(0, count($lastModified));
    }

    public function testMarkAsNewExample3(): void
    {
        $tree = new DomTreeBuilder();
        $comp = new TextNodeComparator($tree);
        $textNodes = $this->getTextNodes($comp);

        $comp->markAsNew(0, 1, ModificationType::CHANGED);

        $lastModified = $comp->getLastModified();
        $this->assertEquals('changed', strval($lastModified[0]->getOutputType()));
    }

    public function testMarkAsNewExample4(): void
    {
        $tree = new DomTreeBuilder();
        $comp = new TextNodeComparator($tree);
        $textNodes = $this->getTextNodes($comp);

        $lastModified = $comp->getLastModified();
        $m = new Modification(ModificationType::REMOVED, ModificationType::REMOVED);
        $lastModified[] = $m;
        $comp->setLastModified($lastModified);

        $comp->markAsNew(0, 1);

        $this->assertEquals('removed', strval($lastModified[0]->getOutputType()));
    }

    public function testRangesEqual(): void
    {
        $tree = new DomTreeBuilder();
        $comp = new TextNodeComparator($tree);
        $textNodes = $this->getTextNodes($comp);

        $this->assertFalse($comp->rangesEqual(0, $comp, 1));
        $this->assertTrue($comp->rangesEqual(0, $comp, 0));
    }

    public function testSkipRangeComparison(): void
    {
        $tree = new DomTreeBuilder();
        $comp = new TextNodeComparator($tree);
        $textNodes = $this->getTextNodes($comp);

        $this->assertFalse($comp->skipRangeComparison(0, 1, $comp));
    }

//    public function testHandlePossibleChangedPart(): void
//    {
//        $tree = new DomTreeBuilder();
//        $comp = new TextNodeComparator($tree);
//        $textNodes = $this->getTextNodes($comp);
//
//        $lastModified = $comp->getLastModified();
//        $m = new Modification(ModificationType::CONFLICT, ModificationType::CONFLICT);
//        $lastModified[] = $m;
//        $comp->setLastModified($lastModified);
//
//        $comp->handlePossibleChangedPart(0, 1, 1, 2, $comp);
//
//        $this->assertEquals('conflict', strval($lastModified[0]->getOutputType()));
//    }

    public function testMarkAsDeletedExample1(): void
    {
        $tree = new DomTreeBuilder();
        $comp = new TextNodeComparator($tree);
        $textNodes = $this->getTextNodes($comp);

        $comp->markAsDeleted(0, 2, $comp, 1);

        $lastModified = $comp->getLastModified();
        $this->assertEquals('removed', strval($lastModified[0]->getOutputType()));
    }

    public function testMarkAsDeletedExample2(): void
    {
        $tree = new DomTreeBuilder();
        $comp = new TextNodeComparator($tree);
        $textNodes = $this->getTextNodes($comp);

        $comp->markAsDeleted(1, 0, $comp, 2);

        $lastModified = $comp->getLastModified();
        $this->assertEquals(0, count($lastModified));
    }

    public function testMarkAsDeletedExample3(): void
    {
        $tree = new DomTreeBuilder();
        $comp = new TextNodeComparator($tree);
        $textNodes = $this->getTextNodes($comp);

        $lastModified = $comp->getLastModified();
        $m = new Modification(ModificationType::REMOVED, ModificationType::REMOVED);
        $lastModified[] = $m;
        $comp->setLastModified($lastModified);

        $comp->markAsDeleted(0, 2, $comp, 1);

        $this->assertEquals('removed', strval($lastModified[0]->getOutputType()));
    }

    public function testExpandWhiteSpace(): void
    {
        $tree = new DomTreeBuilder();
        $comp = new TextNodeComparator($tree);
        $textNodes = $this->getTextNodes($comp);

        $comp->expandWhiteSpace();

        $lastModified = $comp->getLastModified();
        $this->assertEquals(0, count($lastModified));
    }

    public function testIterator(): void
    {
        $tree = new DomTreeBuilder();
        $comp = new TextNodeComparator($tree);
        $textNodes = $this->getTextNodes($comp);

        $iterator = $comp->getIterator();

        $this->assertTrue($iterator->valid());
    }

    public function testSetStartDeletedId(): void
    {
        $tree = new DomTreeBuilder();
        $comp = new TextNodeComparator($tree);

        $id = 456123;
        $comp->setStartDeletedId($id);

        $this->assertEquals($id, $comp->getDeletedId());
    }

    public function testSetStartNewId(): void
    {
        $tree = new DomTreeBuilder();
        $comp = new TextNodeComparator($tree);

        $id = 456123;
        $comp->setStartNewId($id);

        $this->assertEquals($id, $comp->getNewId());
    }

    public function testSetStartChangedI(): void
    {
        $tree = new DomTreeBuilder();
        $comp = new TextNodeComparator($tree);

        $id = 456123;
        $comp->setStartChangedID($id);

        $this->assertEquals($id, $comp->getChangedId());
    }
}
