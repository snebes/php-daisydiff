<?php
/**
 * (c) Steve Nebes <snebes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace SN\DaisyDiff\Parser;

use Masterminds\HTML5;
use SN\DaisyDiff\Contracts\ParserInterface;
use SN\DaisyDiff\Exception\ParsingFailedException;

/**
 * masterminds/html5 parser.
 */
class MastermindsParser implements ParserInterface
{
    /**
     * @param string $html
     * @return \DOMNode
     */
    public function parse(string $html): \DOMNode
    {
        try {
            return (new HTML5())->loadHTMLFragment($html);
        } catch (\Throwable $t) {
            throw new ParsingFailedException($this, $t);
        }
    }
}
