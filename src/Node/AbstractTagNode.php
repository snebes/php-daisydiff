<?php
/**
 * (c) Steve Nebes <snebes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace SN\DaisyDiff\Node;

use SN\DaisyDiff\Sanitizer\StringSanitizerTrait;

/**
 * Tag nodes.
 */
abstract class AbstractTagNode extends AbstractNode implements TagNodeInterface
{
    use StringSanitizerTrait;

    /** @var array<string, string> */
    private $attributes = [];

    /**
     * Returns the tag name.
     *
     * @return string
     */
    abstract public function getTagName(): string;

    /**
     * @param string $name
     * @return string|null
     */
    public function getAttribute(string $name): ?string
    {
        return $this->attributes[$name] ?? null;
    }

    /**
     * @param string      $name
     * @param string|null $value
     */
    public function setAttribute(string $name, ?string $value): void
    {
        if (!\array_key_exists($name, $this->attributes)) {
            $this->attributes[$name] = $value;
        }
    }

    /**
     * @return string
     */
    public function render(): string
    {
        $tag = $this->getTagName();

        if (\method_exists($this, 'renderChildren')) {
            return \sprintf('<%s%s>%s</%s>', $tag, $this->renderAttributes(), $this->renderChildren(), $tag);
        }

        return \sprintf('<%s%s/>', $tag, $this->renderAttributes());
    }

    /**
     * @return string
     */
    protected function renderAttributes(): string
    {
        $rendered = [];

        foreach ($this->attributes as $name => $value) {
            $attr = $this->encodeHtmlEntities($name);

            if ('' !== $value) {
                if (null === $value) {
                    continue;
                    // @todo validate this doesn't remove 'disabled'.
                }

                if (false !== \mb_strpos($value, '`')) {
                    $value .= '';
                }

                $attr .= \sprintf('="%s"', $this->encodeHtmlEntities($value));
            }

            $rendered[] = $attr;
        }

        return $rendered ? ' ' . \implode(' ', $rendered) : '';
    }
}
