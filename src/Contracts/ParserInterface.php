<?php
/**
 * (c) Steve Nebes <snebes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace SN\DaisyDiff\Contracts;

/**
 * A parser transforms an HTML string into a DOMNode tree.
 */
interface ParserInterface
{
    /**
     * Parse a given string and return a DOMNode tree.
     *
     * @param string $html
     * @return \DOMNode
     */
    public function parse(string $html): \DOMNode;
}
