<?php declare(strict_types=1);

namespace DaisyDiff\Html\Dom;

/**
 * Represents an image in HTML. Even though images do not contain any text they are single visible objects on the page.
 * They are logically a TextNode.
 */
class ImageNode extends TextNode
{
    /** @var array */
    private $attributes;

    /**
     * @param  TagNode $parent
     * @param  array   $attributes
     */
    public function __construct(?TagNode $parent, ?array $attributes)
    {
        $this->attributes = array_merge(['src' => ''], (array) $attributes);
        parent::__construct($parent, '<img>' . mb_strtolower($this->attributes['src']) . '</img>');
    }

    /**
     * {@inheritdoc}
     */
    public function isSameText(?Node $other): bool
    {
        if (is_null($other)) {
            return false;
        }

        if (!$other instanceof ImageNode) {
            return false;
        }

        return 0 == strcasecmp($this->getText(), $other->getText());
    }

    /**
     * @return array
     */
    public function getAttributes(): iterable
    {
        return $this->attributes;
    }
}
