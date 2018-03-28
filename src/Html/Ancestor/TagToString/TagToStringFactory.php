<?php declare(strict_types=1);

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
     * @param  TagNode  $node
     * @return TagToString
     */
    public function create(TagNode $node): TagToString
    {
        $sem = $this->getChangeSemantic($node->getQName());

        if (0 == strcasecmp($node->getQName(), 'a')) {
            return new AnchorToString($node, $sem);
        }

        if (0 == strcasecmp($node->getQName(), 'img')) {
            return new NoContentTagToString($node, $sem);
        }

        return new TagToString($node, $sem);
    }

    /**
     * @param  string $str
     * @return string<TagChangeSemantic>
     */
    protected function getChangeSemantic(string $str): string
    {
        if (in_array(mb_strtolower($str), self::$containerTags)) {
            return TagChangeSemantic::MOVED;
        }
        if (in_array(mb_strtolower($str), self::$styleTags)) {
            return TagChangeSemantic::STYLE;
        }

        return TagChangeSemantic::UNKNOWN;
    }
}
