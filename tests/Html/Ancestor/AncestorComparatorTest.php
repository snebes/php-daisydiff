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
use PHPUnit\Framework\TestCase;

/**
 * AncestorComparator Tests.
 */
class AncestorComparatorTest extends TestCase
{
    /** @var AncestorComparator */
    private $comp;

    /** @var Tagnode */
    private $root;

    /** @var Tagnode */
    private $intermediate;

    protected function setUp()
    {
        $this->markTestSkipped();

        $this->root = new TagNode(null, 'root');
        $this->intermediate = new TagNode(null, 'middle');

        $ancestors = [];
        $ancestors[] = $this->root;
        $ancestors[] = $this->intermediate;

        $this->comp = new AncestorComparator($ancestors);
    }

    public function testGetRangeCount(): void
    {
        $this->assertSame(2, $this->comp->getRangeCount());
    }

    public function testRangesEqual(): void
    {
        $this->assertFalse($this->comp->rangesEqual(0, $this->comp, 1));
        $this->assertTrue($this->comp->rangesEqual(0, $this->comp, 0));
    }

    public function testSkipRangeComparison(): void
    {
        $this->assertFalse($this->comp->skipRangeComparison(0, 1, $this->comp));
    }

    public function testGetAncestor(): void
    {
        $this->assertSame($this->root, $this->comp->getAncestor(0));
    }

    /**
     * @expectedException \OutOfBoundsException
     */
    public function testGetAncestorException(): void
    {
        $this->comp->getAncestor(3);
    }

    public function testGetCompareTxt(): void
    {
        $this->assertSame('', $this->comp->getCompareTxt());
    }

    public function testGetResult(): void
    {
        $html = new TagNode(null, 'html');
        $body = new TagNode(null, 'body');

        $secondNodeList = [];
        $secondNodeList[] = $html;
        $secondNodeList[] = $body;

        $other = new AncestorComparator($secondNodeList);

        $this->assertFalse($this->comp->getResult($this->comp)->isChanged());
        $this->assertTrue($this->comp->getResult($other)->isChanged());
    }
}
