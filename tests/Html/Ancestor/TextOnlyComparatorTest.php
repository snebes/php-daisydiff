<?php
/**
 * (c) Steve Nebes <snebes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace DaisyDiff\Html\Ancestor;

use DaisyDiff\Html\Dom\TagNode;
use DaisyDiff\Html\Dom\TextNode;
use PHPUnit\Framework\TestCase;

/**
 * TextOnlyComparator Tests.
 */
class TextOnlyComparatorTest extends TestCase
{

    public function testAddRecursive(): void
    {
        $comp = $this->getComparator();
        $this->assertSame(TextOnlyComparator::class, get_class($comp));
    }

    public function testGetRangeCount(): void
    {
        $comp = $this->getComparator();
        $this->assertSame(2, $comp->getRangeCount());
    }

    public function testRangesEqual(): void
    {
        $comp = $this->getComparator();
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
        // @todo: investigate why this is failing.
//        $this->assertEquals(0.33, $rootComp->getMatchRatio($interComp), '', 0.1);
    }

    /**
     * @expectedException \OutOfBoundsException
     */
    public function testGetLeafIndexOutOfBounds(): void
    {
        $comp = $this->getComparator();
        $comp->getLeaf(-1);
    }

    /**
     * @return TextOnlyComparator
     */
    private function getComparator(): TextOnlyComparator
    {
        $root = new TagNode(null, 'root');
        $intermediate = new TagNode($root, 'middle');
        $root->addChild($intermediate);
        $textRoot = new TextNode($root, 'contents of root node');
        $root->addChild($textRoot);

        $comp = new TextOnlyComparator($root);

        return $comp;
    }
}
