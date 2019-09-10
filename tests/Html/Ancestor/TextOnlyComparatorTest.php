<?php
/**
 * (c) Steve Nebes <snebes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace SN\DaisyDiff\Html\Ancestor;

use SN\DaisyDiff\Html\Dom\TagNode;
use SN\DaisyDiff\Html\Dom\TextNode;
use PHPUnit\Framework\TestCase;

/**
 * TextOnlyComparator Tests.
 */
class TextOnlyComparatorTest extends TestCase
{
    /** @var TextOnlyComparator */
    private $comp;

    protected function setUp(): void
    {
        $root = new TagNode(null, 'root');
        $intermediate = new TagNode($root, 'middle');
        $root->addChild($intermediate);
        $textRoot = new TextNode($root, 'contents of root node');
        $root->addChild($textRoot);

        $this->comp = new TextOnlyComparator($root);
    }

    public function testAddRecursive(): void
    {
        $this->assertInstanceOf(TextOnlyComparator::class, $this->comp);
    }

    public function testGetRangeCount(): void
    {
        $this->assertSame(2, $this->comp->getRangeCount());
    }

    public function testRangesEqual(): void
    {
        $this->assertTrue($this->comp->rangesEqual(0, $this->comp, 0));
    }

    public function testSkipRangeComparison(): void
    {
        $this->assertFalse($this->comp->skipRangeComparison(0, 1, $this->comp));
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
        $this->assertEquals(0.33, $rootComp->getMatchRatio($interComp), '', 0.1);
    }

    /**
     * @expectedException \Exception
     */
    public function testGetLeafIndexOutOfBounds(): void
    {
        try {
            $this->comp->getLeaf(100);
        } catch (\Exception $e) {
            $this->assertInstanceOf(\OutOfBoundsException::class, $e);

            throw $e;
        }
    }
}
