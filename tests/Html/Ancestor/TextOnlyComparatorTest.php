<?php declare(strict_types=1);

namespace DaisyDiff\Html\Ancestor;

use DaisyDiff\Html\Dom\TagNode;
use DaisyDiff\Html\Dom\TextNode;
use DaisyDiff\RangeDifferencer\RangeDifference;
use DaisyDiff\RangeDifferencer\RangeDifferencer;
use OutOfBoundsException;
use PHPUnit\Framework\TestCase;

/**
 * TextOnlyComparator Tests.
 */
class TextOnlyComparatorTest extends TestCase
{
    public function testAddRecursive(): void
    {
        $root = new TagNode(null, 'root');
        $intermediate = new TagNode($root, 'middle');
        $root->addChild($intermediate);

        $textRoot = new TextNode($root, 'contents of root node');
        $root->addChild($textRoot);

        $tagComp = new TextOnlyComparator($root);

        $this->assertEquals(TextOnlyComparator::class, get_class($tagComp));
    }

    public function testGetRangeCount(): void
    {
        $root = new TagNode(null, 'root');
        $intermediate = new TagNode($root, 'middle');
        $root->addChild($intermediate);

        $textRoot = new TextNode($root, 'contents of root node');
        $root->addChild($textRoot);

        $tagComp = new TextOnlyComparator($root);

        $this->assertEquals(2, $tagComp->getRangeCount());
    }

    public function testRangesEqual(): void
    {
        $root = new TagNode(null, 'root');
        $intermediate = new TagNode($root, 'middle');
        $root->addChild($intermediate);

        $textRoot = new TextNode($root, 'contents of root node');
        $root->addChild($textRoot);

        $comp = new TextOnlyComparator($root);

        $this->assertTrue($comp->rangesEqual(0, $comp, 0));
    }

    public function testSkipRangeComparison(): void
    {
        $root = new TagNode(null, 'root');
        $intermediate = new TagNode($root, 'middle');
        $root->addChild($intermediate);

        $comp = new TextOnlyComparator($root);

        $this->assertFalse($comp->skipRangeComparison(0, 1, $comp));
    }

    /**
     * @group incomplete
     */
    public function testGetMatchRatio(): void
    {
        $root = new TagNode(null, 'root');
        $intermediate = new TagNode($root, 'middle');
        $root->addChild($intermediate);

        $textInter = new TextNode($intermediate, 'contents of inter node');
        $intermediate->addChild($textInter);
        $textRoot = new TextNode($root, 'contents of root node');
        $root->addChild($textRoot);

        $rootComp  = new TextOnlyComparator($root);
        $interComp = new TextOnlyComparator($intermediate);

        $this->assertEquals(0.0, $rootComp->getMatchRatio($rootComp), '', 0.1);
        $this->assertEquals(0.33, $rootComp->getMatchRatio($interComp), '', 0.1);
    }

    /**
     * @expectedException OutOfBoundsException
     */
    public function testGetLeafIndexOutOfBounds(): void
    {
        $root = new TagNode(null, 'root');
        $intermediate = new TagNode($root, 'middle');
        $root->addChild($intermediate);

        $comp = new TextOnlyComparator($root);

        try {
            $comp->getLeaf(-1);
        } catch (OutOfBoundsException $e) {
            $this->assertEquals('Index: -1, Size: 0', $e->getMessage());
            throw $e;
        }
    }
}
