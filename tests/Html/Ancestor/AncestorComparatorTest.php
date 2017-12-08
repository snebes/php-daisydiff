<?php declare(strict_types=1);

namespace DaisyDiff\Html\Ancestor;

use DaisyDiff\Html\Dom\TagNode;
use OutOfBoundsException;
use PHPUnit\Framework\TestCase;

/**
 * AncestorComparator Tests.
 */
class AncestorComparatorTest extends TestCase
{
    public function testGetRangeCount(): void
    {
        $root = new TagNode(null, 'root');
        $intermediate = new TagNode(null, 'middle');

        $ancestors = [];
        $ancestors[] = $root;
        $ancestors[] = $intermediate;

        $comp = new AncestorComparator($ancestors);

        $this->assertEquals(2, $comp->getRangeCount());
    }

    public function testRangesEqual(): void
    {
        $root = new TagNode(null, 'root');
        $intermediate = new TagNode(null, 'middle');

        $ancestors = [];
        $ancestors[] = $root;
        $ancestors[] = $intermediate;

        $comp = new AncestorComparator($ancestors);

        $this->assertFalse($comp->rangesEqual(0, $comp, 1));
        $this->assertTrue($comp->rangesEqual(0, $comp, 0));
    }

    public function testSkipRangeComparison(): void
    {
        $root = new TagNode(null, 'root');
        $intermediate = new TagNode(null, 'middle');

        $ancestors = [];
        $ancestors[] = $root;
        $ancestors[] = $intermediate;

        $comp = new AncestorComparator($ancestors);

        $this->assertFalse($comp->skipRangeComparison(0, 1, $comp));
    }

    public function testGetAncestor(): void
    {
        $root = new TagNode(null, 'root');
        $intermediate = new TagNode(null, 'middle');

        $ancestors = [];
        $ancestors[] = $root;
        $ancestors[] = $intermediate;

        $comp = new AncestorComparator($ancestors);

        $this->assertEquals($root, $comp->getAncestor(0));
    }

    /**
     * @expectedException OutOfBoundsException
     */
    public function testGetAncestorException(): void
    {
        $root = new TagNode(null, 'root');
        $intermediate = new TagNode(null, 'middle');

        $ancestors = [];
        $ancestors[] = $root;
        $ancestors[] = $intermediate;

        $comp = new AncestorComparator($ancestors);

        try {
            $comp->getAncestor(3);
        } catch (OutOfBoundsException $e) {
            $this->assertEquals('Index: 3, Size: 2', $e->getMessage());
            throw $e;
        }
    }

    public function testGetCompareTxt(): void
    {
        $root = new TagNode(null, 'root');
        $intermediate = new TagNode(null, 'middle');

        $ancestors = [];
        $ancestors[] = $root;
        $ancestors[] = $intermediate;

        $comp = new AncestorComparator($ancestors);

        $this->assertEquals('', $comp->getCompareTxt());
    }

   public function testGetResult(): void
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

       $comp  = new AncestorComparator($firstNodeList);
       $other = new AncestorComparator($secondNodeList);

       $this->assertFalse($comp->getResult($comp)->isChanged());
       $this->assertTrue($comp->getResult($other)->isChanged());
   }
}
