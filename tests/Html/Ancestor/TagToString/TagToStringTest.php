<?php
/**
 * (c) Steve Nebes <snebes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace SN\DaisyDiff\Html\Ancestor\TagToString;

use SN\DaisyDiff\Html\Ancestor\TagChangeSemantic;
use SN\DaisyDiff\Html\Dom\TagNode;
use PHPUnit\Framework\TestCase;

/**
 * TagToString Tests.
 */
class TagToStringTest extends TestCase
{
    public function testDiffs(): void
    {
        $root = new TagNode(null, 'root');
        $intermediate = new TagNode($root, 'middle');
        $root->addChild($intermediate);

        $tagToString = new TagToString($root, TagChangeSemantic::STYLE);

        $this->assertEquals('!diff-root!', $tagToString->getDescription());
    }
}
