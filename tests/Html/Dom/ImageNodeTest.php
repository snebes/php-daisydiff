<?php
/**
 * (c) Steve Nebes <snebes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace SN\DaisyDiff\Html\Dom;

use PHPUnit\Framework\TestCase;

/**
 * ImageNode Tests.
 */
class ImageNodeTest extends TestCase
{
    public function testIsSameText(): void
    {
        $nodeAttrs = ['src' => 'location of image tag'];
        $attrs = ['src' => 'different location for this tag'];

        $root = new TagNode(null, 'root', $nodeAttrs);
        $img = new TagNode($root, 'img', $nodeAttrs);

        $imgNode = new ImageNode($img, $nodeAttrs);
        $rootNode = new ImageNode($root, $nodeAttrs);
        $compareNode = new ImageNode($root, $attrs);

        $this->assertTrue($imgNode->isSameText($rootNode));
        $this->assertFalse($compareNode->isSameText($rootNode));
        $this->assertFalse($imgNode->isSameText(null));
        $this->assertFalse($rootNode->isSameText($root));

        $this->assertSame('src', \key($imgNode->getAttributes()));
    }

    public function testGetAttributes(): void
    {
        $nodeAttrs = ['src' => 'location of image tag'];
        $root = new TagNode(null, 'root', $nodeAttrs);

        $this->assertEquals($nodeAttrs, $root->getAttributes());
    }
}
