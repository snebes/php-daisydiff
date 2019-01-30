<?php
/**
 * (c) Steve Nebes <snebes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace SN\DaisyDiff\Html\Dom;

/**
 * Represents whitespace in the HTML file.
 */
class WhiteSpaceNode extends TextNode
{
    /**
     * @param TagNode|null $parent
     * @param string       $s
     * @param Node|null     $like
     */
    public function __construct(?TagNode $parent, string $s, ?Node $like)
    {
        parent::__construct($parent, $s);

        if ($like instanceof TextNode) {
            $newModification = clone $like->getModification();
            $newModification->setFirstOfId(false);
            $this->setModification($newModification);
        }
    }

    /**
     * @param string $c
     * @return bool
     */
    public static function isWhiteSpace(string $c): bool
    {
        switch ($c) {
            case ' ':
            case "\t":
            case "\r":
            case "\n":
                return true;
            default:
                return false;
        }
    }
}
