<?php
/**
 * (c) Steve Nebes <snebes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace DaisyDiff\Html\Dom;

use DaisyDiff\Html\Modification\Modification;
use DaisyDiff\Html\Modification\ModificationType;
use PHPUnit\Framework\TestCase;

/**
 * BodyNode Tests.
 */
class BodyNodeTest extends TestCase
{
    public function testCopyTreeWithNoChildren(): void
    {
        $body = new BodyNode();
        $copy = $body->copyTree();

        $this->assertEquals($body, $copy);
        $this->assertNotSame($body, $copy);

    }

    public function testCopyTreeWithChildren(): void
    {
        $body = new BodyNode();
        $intermediate = new TagNode($body, 'middle');
        $leaf = new TagNode($intermediate, 'leaf');
        $copy = $body->copyTree();

        $this->assertEquals($body, $copy);
        $this->assertNotSame($body, $copy);
    }

    public function testGetMinimalDeletedSet(): void
    {
        $body = new BodyNode();
        $intermediate = new TagNode($body, 'middle');
        $leaf = new TagNode($intermediate, 'leaf');
        new TextNode($leaf, 'text');

        $nodes = [];
        $this->assertSame($nodes, $body->getMinimalDeletedSet(0));
        $this->assertSame($nodes, $leaf->getMinimalDeletedSet(0));

        $text = new TextNode($leaf, 'deleted');
        $mod = new Modification(ModificationType::REMOVED, ModificationType::REMOVED);
        $mod->setId(1);
        $text->setModification($mod);

        $this->assertSame([$text], $body->getMinimalDeletedSet(1));
    }
}
