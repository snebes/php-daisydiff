<?php declare(strict_types=1);

namespace DaisyDiff\Html\Dom;

use PHPUnit\Framework\TestCase;

/**
 * ImageNode Tests.
 *
 * @covers DaisyDiff\Html\Dom\ImageNode::__construct
 */
class ImageNodeTest extends TestCase
{
    /**
     * @covers DaisyDiff\Html\Dom\ImageNode::isSameText
     */
    public function testIsSameText(): void
    {
        $nodeAttrs = ['src' => 'location of image tag'];
        $attrs     = ['src' => 'different location for this tag'];

        $root = new TagNode(null, 'root', $nodeAttrs);
        $img  = new TagNode($root, 'img', $nodeAttrs);

        $imgNode = new ImageNode($img, $nodeAttrs);
        $rootNode = new ImageNode($root, $nodeAttrs);
        $compareNode = new ImageNode($root, $attrs);

        $this->assertTrue($imgNode->isSameText($rootNode));
        $this->assertFalse($compareNode->isSameText($rootNode));
        $this->assertFalse($imgNode->isSameText(null));
        $this->assertFalse($rootNode->isSameText($root));
    }
}
