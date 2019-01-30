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
use SN\DaisyDiff\Html\Dom\TagNode;
use SN\DaisyDiff\Html\Dom\TextNode;
use SN\DaisyDiff\Html\Modification\Modification;
use SN\DaisyDiff\Html\Modification\ModificationType;
use SN\DaisyDiff\Xml\XMLReader;
use PHPUnit\Framework\TestCase;

/**
 * TextNodeComparator Tests.
 */
class TextNodeComparatorTest extends TestCase
{
    public function testTextNodeComparator(): void
    {
        $p = new TagNode(null, 'p');
        $text = new TextNode($p, 'contents of p node');

        $domTree = new DomTreeBuilder();
        $comp = new TextNodeComparator($domTree);

        $refProp = new \ReflectionProperty($comp, 'textNodes');
        $refProp->setAccessible(true);

        $textNodes = $refProp->getValue($comp);
        $textNodes[] = $text;
        $refProp->setValue($comp, $textNodes);

        $this->assertSame('<body>', (string) $comp->getBodyNode());
        $this->assertSame('contents of p node', (string) $comp->getTextNode(0));
        $this->assertSame(1, $comp->getRangeCount());
    }

    public function testMarkAsNewExample1(): void
    {
        $tree = new DomTreeBuilder();
        $comp = new TextNodeComparator($tree);
        $this->getTextNodes($comp);

        $comp->markAsNew(0, 1);

        $lastModified = $comp->getLastModified();
        $this->assertSame('added', $lastModified[0]->getOutputType());
    }

    public function testMarkAsNewExample2(): void
    {
        $tree = new DomTreeBuilder();
        $comp = new TextNodeComparator($tree);
        $this->getTextNodes($comp);

        $comp->markAsNew(1, 0);

        $lastModified = $comp->getLastModified();
        $this->assertCount(0, $lastModified);
    }

    public function testMarkAsNewExample3(): void
    {
        $tree = new DomTreeBuilder();
        $comp = new TextNodeComparator($tree);
        $this->getTextNodes($comp);

        $comp->markAsNew(0, 1, ModificationType::CHANGED);

        $lastModified = $comp->getLastModified();
        $this->assertSame('changed', $lastModified[0]->getOutputType());
    }

    public function testMarkAsNewExample4(): void
    {
        $tree = new DomTreeBuilder();
        $comp = new TextNodeComparator($tree);
        $this->getTextNodes($comp);

        $lastModified = $comp->getLastModified();
        $m = new Modification(ModificationType::REMOVED, ModificationType::REMOVED);
        $lastModified[] = $m;
        $comp->setLastModified($lastModified);

        $comp->markAsNew(0, 1);

        $this->assertSame('removed', $lastModified[0]->getOutputType());
    }

    public function testRangesEqual(): void
    {
        $tree = new DomTreeBuilder();
        $comp = new TextNodeComparator($tree);
        $this->getTextNodes($comp);

        $this->assertFalse($comp->rangesEqual(0, $comp, 1));
        $this->assertTrue($comp->rangesEqual(0, $comp, 0));
    }

    public function testSkipRangeComparison(): void
    {
        $tree = new DomTreeBuilder();
        $comp = new TextNodeComparator($tree);
        $this->getTextNodes($comp);

        $this->assertFalse($comp->skipRangeComparison(0, 1, $comp));
    }

   public function testHandlePossibleChangedPart(): void
   {
       $tree = new DomTreeBuilder();
       $comp = new TextNodeComparator($tree);
       $this->getTextNodes($comp);

       $lastModified = $comp->getLastModified();
       $m = new Modification(ModificationType::CONFLICT, ModificationType::CONFLICT);
       $lastModified[] = $m;
       $comp->setLastModified($lastModified);

       $comp->handlePossibleChangedPart(0, 1, 1, 2, $comp);

       $this->assertSame('conflict', $lastModified[0]->getOutputType());
   }

    public function testMarkAsDeletedExample1(): void
    {
        $tree = new DomTreeBuilder();
        $comp = new TextNodeComparator($tree);
        $this->getTextNodes($comp);

        $comp->markAsDeleted(0, 2, $comp, 1);

        $lastModified = $comp->getLastModified();
        $this->assertSame('removed', $lastModified[0]->getOutputType());
    }

    public function testMarkAsDeletedExample2(): void
    {
        $tree = new DomTreeBuilder();
        $comp = new TextNodeComparator($tree);
        $this->getTextNodes($comp);

        $comp->markAsDeleted(1, 0, $comp, 2);

        $lastModified = $comp->getLastModified();
        $this->assertCount(0, $lastModified);
    }

    public function testMarkAsDeletedExample3(): void
    {
        $tree = new DomTreeBuilder();
        $comp = new TextNodeComparator($tree);
        $this->getTextNodes($comp);

        $lastModified = $comp->getLastModified();
        $m = new Modification(ModificationType::REMOVED, ModificationType::REMOVED);
        $lastModified[] = $m;
        $comp->setLastModified($lastModified);

        $comp->markAsDeleted(0, 2, $comp, 1);

        $this->assertSame('removed', $lastModified[0]->getOutputType());
    }

    public function testExpandWhiteSpace(): void
    {
        $tree = new DomTreeBuilder();
        $comp = new TextNodeComparator($tree);
        $this->getTextNodes($comp);

        $comp->expandWhiteSpace();

        $lastModified = $comp->getLastModified();
        $this->assertCount(0, $lastModified);
    }

    public function testIterator(): void
    {
        $tree = new DomTreeBuilder();
        $comp = new TextNodeComparator($tree);
        $this->getTextNodes($comp);

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

    /**
     * @expectedException \Exception
     */
    public function testGetTextNode(): void
    {
        $tree = new DomTreeBuilder();
        $comp = new TextNodeComparator($tree);

        try {
            $comp->getTextNode(10);
        } catch (\Exception $e) {
            $this->assertInstanceOf(\OutOfBoundsException::class, $e);

            throw $e;
        }
    }

    public function testComplexExample1(): void
    {
        $html = <<<HTML
<p> <b> in bold </b> in p <span>in span </span></p>
HTML;

        $tree = new DomTreeBuilder();
        $reader = new XMLReader($tree);
        $reader->parse($html);

        $comp = new TextNodeComparator($tree);

        $comp->markAsDeleted(2, 4, $comp, 2);
        $comp->markAsNew(5, 6);

        $lastModified = $comp->getLastModified();
        $this->assertSame('added', $lastModified[0]->getOutputType());
        $this->assertSame('removed', $lastModified[0]->getPrevious()->getOutputType());
    }

    /**
     * @param TextNodeComparator $comp
     * @return TextNode[]
     */
    private function getTextNodes(TextNodeComparator $comp): array
    {
        $p = new TagNode(null, 'p');
        $text = new TextNode($p, 'contents of p node');

        $b = new TagNode(null, 'b');
        $boldText = new TextNode($b, 'contents of bold node');

        $refProp = new \ReflectionProperty($comp, 'textNodes');
        $refProp->setAccessible(true);

        $textNodes = $refProp->getValue($comp);
        $textNodes[] = $text;
        $textNodes[] = $boldText;
        $refProp->setValue($comp, $textNodes);

        return $textNodes;
    }
}
