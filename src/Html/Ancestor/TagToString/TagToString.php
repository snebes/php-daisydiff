<?php
/**
 * (c) Steve Nebes <snebes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace SN\DaisyDiff\Html\Ancestor\TagToString;

use SN\DaisyDiff\Html\Ancestor\TagChangeSemantic;
use SN\DaisyDiff\Html\ChangeText;
use SN\DaisyDiff\Html\Dom\TagNode;
use SN\DaisyDiff\Html\Modification\HtmlLayoutChange;

/**
 * TagToString
 */
class TagToString
{
    /** @var TagNode */
    protected $node;

    /** @var string */
    protected $sem;

    /** @var HtmlLayoutChange */
    protected $htmlLayoutChange;

    /**
     * @param TagNode $node
     * @param string  $sem
     */
    public function __construct(TagNode $node, string $sem)
    {
        $this->node = $node;
        $this->sem = $sem;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->getString('diff-' . $this->node->getQName());
    }

    /**
     * @param ChangeText $text
     */
    public function getRemovedDescription(ChangeText $text): void
    {
        $this->htmlLayoutChange = new HtmlLayoutChange();
        $this->htmlLayoutChange->setEndingTag($this->node->getEndTag());
        $this->htmlLayoutChange->setOpeningTag($this->node->getOpeningTag());
        $this->htmlLayoutChange->setType(HtmlLayoutChange::TAG_REMOVED);

        if ($this->sem === TagChangeSemantic::MOVED) {
            $text->characters(\sprintf('%s %s ', $this->getMovedOutOf(), \mb_strtolower($this->getArticle())));
            $text->startElement('b');
            $text->characters(\mb_strtolower($this->getDescription()));
            $text->endElement('b');
        } elseif ($this->sem === TagChangeSemantic::STYLE) {
            $text->startElement('b');
            $text->characters($this->getDescription());
            $text->endElement('b');
            $text->characters(\sprintf(' %s', \mb_strtolower($this->getStyleRemoved())));
        } else {
            $text->startElement('b');
            $text->characters($this->getDescription());
            $text->endElement('b');
            $text->characters(\sprintf(' %s', \mb_strtolower($this->getRemoved())));
        }

        $this->addAttributes($text, $this->node->getAttributes());
        $text->characters('.');
    }

    /**
     * @param ChangeText $text
     */
    public function getAddedDescription(ChangeText $text): void
    {
        $this->htmlLayoutChange = new HtmlLayoutChange();
        $this->htmlLayoutChange->setEndingTag($this->node->getEndTag());
        $this->htmlLayoutChange->setOpeningTag($this->node->getOpeningTag());
        $this->htmlLayoutChange->setType(HtmlLayoutChange::TAG_ADDED);

        if ($this->sem === TagChangeSemantic::MOVED) {
            $text->characters(\sprintf('%s %s ', $this->getMovedTo(), \mb_strtolower($this->getArticle())));
            $text->startElement('b');
            $text->characters(\mb_strtolower($this->getDescription()));
            $text->endElement('b');
        } elseif ($this->sem === TagChangeSemantic::STYLE) {
            $text->startElement('b');
            $text->characters($this->getDescription());
            $text->endElement('b');
            $text->characters(\sprintf(' %s', \mb_strtolower($this->getStyleAdded())));
        } else {
            $text->startElement('b');
            $text->characters($this->getDescription());
            $text->endElement('b');
            $text->characters(\sprintf(' %s', \mb_strtolower($this->getAdded())));
        }

        $this->addAttributes($text, $this->node->getAttributes());
        $text->characters('.');
    }

    /**
     * @return string
     */
    protected function getMovedTo(): string
    {
        return $this->getString('diff-movedto');
    }

    /**
     * @return string
     */
    protected function getStyleAdded(): string
    {
        return $this->getString('diff-styleadded');
    }

    /**
     * @return string
     */
    protected function getAdded(): string
    {
        return $this->getString('diff-added');
    }

    /**
     * @return string
     */
    protected function getMovedOutOf(): string
    {
        return $this->getString('diff-movedoutof');
    }

    /**
     * @return string
     */
    protected function getStyleRemoved(): string
    {
        return $this->getString('diff-styleremoved');
    }

    /**
     * @return string
     */
    protected function getRemoved(): string
    {
        return $this->getString('diff-removed');
    }

    /**
     * @param ChangeText $text
     * @param array      $attributes
     */
    protected function addAttributes(ChangeText $text, array $attributes): void
    {
        if (empty($attributes)) {
            return;
        }

        $arr = [];

        /**
         * @var string $qName
         * @var string $value
         */
        foreach ($attributes as $qName => $value) {
            $arr[] = \sprintf('%s %s', $this->translateArgument($qName), $value);
        }

        $text->characters(\sprintf('%s %s', \mb_strtolower($this->getWith()), \implode(', ', $arr)));
    }

    /**
     * @return string
     */
    protected function getAnd(): string
    {
        return $this->getString('diff-and');
    }

    /**
     * @return string
     */
    protected function getWith(): string
    {
        return $this->getString('diff-with');
    }

    /**
     * @param string $name
     * @return string
     */
    protected function translateArgument(string $name): string
    {
        if (0 === \strcasecmp($name, 'src')) {
            return \mb_strtolower($this->getSource());
        }

        if (0 === \strcasecmp($name, 'width')) {
            return \mb_strtolower($this->getWidth());
        }

        if (0 === \strcasecmp($name, 'height')) {
            return \mb_strtolower($this->getHeight());
        }

        return $name;
    }

    /**
     * @return string
     */
    protected function getWidth(): string
    {
        return $this->getString('diff-width');
    }

    /**
     * @return string
     */
    protected function getHeight(): string
    {
        return $this->getString('diff-height');
    }

    /**
     * @return string
     */
    protected function getSource(): string
    {
        return $this->getString('diff-source');
    }

    /**
     * @return string
     */
    protected function getArticle(): string
    {
        return $this->getString(\sprintf('diff-%s-article', $this->node->getQName()));
    }

    /**
     * @param string $key
     * @return string
     */
    public function getString(string $key): string
    {
        $trans = [
            'diff-movedto'            => 'Moved to',
            'diff-styleadded'         => 'Style added',
            'diff-added'              => 'Added',
            'diff-changedto'          => 'Changed to',
            'diff-movedoutof'         => 'Moved out of',
            'diff-styleremoved'       => 'Style removed',
            'diff-removed'            => 'Removed',
            'diff-changedfrom'        => 'Changed from',
            'diff-source'             => 'Source',
            'diff-withdestination'    => 'With destination',
            'diff-and'                => 'And',
            'diff-with'               => 'With',
            'diff-width'              => 'Width',
            'diff-height'             => 'Height',
            'diff-html-article'       => 'A',
            'diff-html'               => 'Html page',
            'diff-body-article'       => 'A',
            'diff-body'               => 'Html document',
            'diff-p-article'          => 'A',
            'diff-p'                  => 'Paragraph',
            'diff-blockquote-article' => 'A',
            'diff-blockquote'         => 'Quote',
            'diff-h1-article'         => 'A',
            'diff-h1'                 => 'Heading (level 1)',
            'diff-h2-article'         => 'A',
            'diff-h2'                 => 'Heading (level 2)',
            'diff-h3-article'         => 'A',
            'diff-h3'                 => 'Heading (level 3)',
            'diff-h4-article'         => 'A',
            'diff-h4'                 => 'Heading (level 4)',
            'diff-h5-article'         => 'A',
            'diff-h5'                 => 'Heading (level 5)',
            'diff-pre-article'        => 'A',
            'diff-pre'                => 'Preformatted block',
            'diff-div-article'        => 'A',
            'diff-div'                => 'Division',
            'diff-ul-article'         => 'An',
            'diff-ul'                 => 'Unordered list',
            'diff-ol-article'         => 'An',
            'diff-ol'                 => 'Ordered list',
            'diff-li-article'         => 'A',
            'diff-li'                 => 'List item',
            'diff-table-article'      => 'A',
            'diff-table'              => 'Table',
            'diff-tbody-article'      => 'A',
            'diff-tbody'              => "Table's content",
            'diff-tr-article'         => 'A',
            'diff-tr'                 => 'Row',
            'diff-td-article'         => 'A',
            'diff-td'                 => 'Cell',
            'diff-th-article'         => 'A',
            'diff-th'                 => 'Header',
            'diff-br-article'         => 'A',
            'diff-br'                 => 'Break',
            'diff-hr-article'         => 'A',
            'diff-hr'                 => 'Horizontal rule',
            'diff-code-article'       => 'A',
            'diff-code'               => 'Computer code block',
            'diff-dl-article'         => 'A',
            'diff-dl'                 => 'Definition list',
            'diff-dt-article'         => 'A',
            'diff-dt'                 => 'Definition term',
            'diff-dd-article'         => 'A',
            'diff-dd'                 => 'Definition',
            'diff-input-article'      => 'An',
            'diff-input'              => 'Input',
            'diff-form-article'       => 'A',
            'diff-form'               => 'Form',
            'diff-img-article'        => 'An',
            'diff-img'                => 'Image',
            'diff-span-article'       => 'A',
            'diff-span'               => 'Span',
            'diff-a-article'          => 'A',
            'diff-a'                  => 'Link',
            'diff-i'                  => 'Italics',
            'diff-b'                  => 'Bold',
            'diff-strong'             => 'Strong',
            'diff-em'                 => 'Emphasis',
            'diff-font'               => 'Font',
            'diff-big'                => 'Big',
            'diff-del'                => 'Deleted',
            'diff-tt'                 => 'Fixed width',
            'diff-sub'                => 'Subscript',
            'diff-sup'                => 'Superscript',
            'diff-strike'             => 'Strikethrough',
        ];

        return $trans[$key] ?? \sprintf('!%s!', $key);
    }

    /**
     * @return HtmlLayoutChange
     */
    public function getHtmlLayoutChange(): HtmlLayoutChange
    {
        return $this->htmlLayoutChange;
    }
}
