<?php
/**
 * (c) Steve Nebes <snebes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace DaisyDiff\Html\Ancestor\TagToString;

use DaisyDiff\Html\Ancestor\TagChangeSemantic;
use DaisyDiff\Html\Dom\TagNode;

/**
 * TagToString Factory
 */
class TagToStringFactory
{
    /** @var string[] */
    private static $containerTags = [
        'html',
        'body',
        'p',
        'blockquote',
        'h1',
        'h2',
        'h3',
        'h4',
        'h5',
        'pre',
        'div',
        'ul',
        'ol',
        'li',
        'table',
        'tbody',
        'tr',
        'td',
        'th',
        'br',
        'hr',
        'code',
        'dl',
        'dt',
        'dd',
        'input',
        'form',
        'img',
        // in-line tags that can be considered containers not styles
        'span',
        'a',
    ];

    /** @var string[] */
    private static $styleTags = [
        'i',
        'b',
        'strong',
        'em',
        'font',
        'big',
        'del',
        'tt',
        'sub',
        'sup',
        'strike',
    ];

    /**
     * @param TagNode $node
     * @return TagToString
     */
    public function create(TagNode $node): TagToString
    {
        $sem = $this->getChangeSemantic($node->getQName());

        if ('a' === $node->getQName()) {
            return new AnchorToString($node, $sem);
        }

        if ('img' === $node->getQName()) {
            return new NoContentTagToString($node, $sem);
        }

        return new TagToString($node, $sem);
    }

    /**
     * @param string $qName
     * @return string
     */
    private function getChangeSemantic(string $qName): string
    {
        if (\in_array($qName, self::$containerTags)) {
            return TagChangeSemantic::MOVED;
        }

        if (\in_array($qName, self::$styleTags)) {
            return TagChangeSemantic::STYLE;
        }

        return TagChangeSemantic::UNKNOWN;
    }
}
