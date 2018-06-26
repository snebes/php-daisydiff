<?php

declare(strict_types=1);

namespace DaisyDiff\Html\Dom;

/**
 * Represents whitespace in the HTML file.
 */
class WhiteSpaceNode extends TextNode
{
    /**
     * @param TagNode $parent
     * @param string  $s
     * @param Node $like
     */
    public function __construct(?TagNode $parent, string $s, ?Node $like = null)
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
        switch (mb_substr($c, 0, 1)) {
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
