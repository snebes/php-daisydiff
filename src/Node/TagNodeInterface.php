<?php
/**
 * (c) Steve Nebes <snebes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace SN\DaisyDiff\Node;

/**
 * Represents a tag node with attributes.
 */
interface TagNodeInterface
{
    /**
     * Return the value of this nodes' given attribute.
     * Return null if the attribute does not exist.
     *
     * @param string $name
     * @return string|null
     */
    public function getAttribute(string $name): ?string;

    /**
     * Set the value of an attribute on this node.
     *
     * @param string      $name
     * @param string|null $value
     */
    public function setAttribute(string $name, ?string $value): void;
}
