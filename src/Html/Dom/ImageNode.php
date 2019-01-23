<?php
/**
 * (c) Steve Nebes <snebes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace DaisyDiff\Html\Dom;

/**
 * Represents an image in HTML. Even though images do not contain any text they are single visible objects on the page.
 * They are logically a TextNode.
 */
class ImageNode extends TextNode
{
    /** @var array */
    private $attributes = [];

    /**
     * @param TagNode $parent
     * @param array   $attributes
     */
    public function __construct(?TagNode $parent, array $attributes = [])
    {
        parent::__construct($parent, \sprintf('<img>%s</img>', $attributes['src'] ?? ''));
        $this->attributes = $attributes;
    }

    /**
     * {@inheritdoc}
     */
    public function isSameText(?Node $other): bool
    {
        if (null === $other || !$other instanceof ImageNode) {
            return false;
        }

        return $this->getText() === $other->getText();
    }

    /**
     * @return array
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }
}
