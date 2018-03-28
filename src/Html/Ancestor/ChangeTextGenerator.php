<?php declare(strict_types=1);

namespace DaisyDiff\Html\Ancestor;

use DaisyDiff\Html\Ancestor\TagToString\TagToStringFactory;
use DaisyDiff\Html\Dom\TagNode;
use DaisyDiff\Html\Modification\HtmlLayoutChange;
use DaisyDiff\RangeDifferencer\RangeDifference;

/**
 * ChangeTextGenerator
 */
class ChangeTextGenerator
{
    /** @var HtmlLayoutChange[] */
    private $htmlLayoutChanges = [];

    /** @var AncestorComparator */
    private $ancestorComparator;

    /** @var AncestorComparator */
    private $other;

    /** @var TagToStringFactory */
    private $factory;

    /** @const int */
    private const MAX_OUTPUT_LINE_LENGTH = 55;

    /**
     * @param  AncestorComparator $ancestorComparator
     * @param  AncestorComparator $other
     */
    public function __construct(AncestorComparator $ancestorComparator, AncestorComparator $other)
    {
        $this->ancestorComparator = $ancestorComparator;
        $this->other = $other;

        $this->factory = new TagToStringFactory();
    }

    /**
     * @param  RangeDifference[] $differences
     * @return ChangeText
     */
    public function getChanged(array $differences): ChangeText
    {
        $text = new ChangeText(self::MAX_OUTPUT_LINE_LENGTH);
        $rootListOpened = false;

        if (count($differences) > 1) {
            $text->addHtml('<ul class="changelist">');
            $rootListOpened = true;
        }

        for ($j = 0; $j < count($differences); $j++) {
            $d = $differences[$j];
            $lvl1ListOpened = false;

            if ($rootListOpened) {
                $text->addHtml('<li>');
            }

            if ($d->leftLength() + $d->rightLength() > 1) {
                $text->addHtml('<ul class="changelist">');
                $lvl1ListOpened = true;
            }

            // Left are the old ones.
            for ($i = $d->leftStart(); $i < $d->leftEnd(); $i++) {
                if ($lvl1ListOpened) {
                    $text->addHtml('<li>');
                }

                // Add a bullet for an old tag.
                $this->addTagOld($text, $this->other->getAncestor($i));

                if ($lvl1ListOpened) {
                    $text->addHtml('</li>');
                }
            }

            // Right are the new ones.
            for ($i = $d->rightStart(); $i < $d->rightEnd(); $i++) {
                if ($lvl1ListOpened) {
                    $text->addHtml('<li>');
                }

                // Add a bullet for an old tag.
                $this->addTagNew($text, $this->getAncestor($i));

                if ($lvl1ListOpened) {
                    $text->addHtml('</li>');
                }
            }

            if ($lvl1ListOpened) {
                $text->addHtml('</ul>');
            }

            if ($rootListOpened) {
                $text->addHtml('</li>');
            }
        }

        if ($rootListOpened) {
            $text->addHtml('</ul>');
        }

        return $text;
    }

    /**
     * @param ChangeText $text
     * @param TagNode $ancestor
     */
    private function addTagOld(ChangeText $text, TagNode $ancestor): void
    {
        $tagToString = $this->factory->create($ancestor);
        $tagToString->getRemovedDescription($text);
        $this->htmlLayoutChanges[] = $tagToString->getHtmlLayoutChange();
    }

    /**
     * @param ChangeText $text
     * @param TagNode $ancestor
     */
    private function addTagNew(ChangeText $text, TagNode $ancestor): void
    {
        $tagToString = $this->factory->create($ancestor);
        $tagToString->getAddedDescription($text);
        $this->htmlLayoutChanges[] = $tagToString->getHtmlLayoutChange();
    }

    /**
     * @param  int $i
     * @return TagNode
     */
    private function getAncestor(int $i): TagNode
    {
        return $this->ancestorComparator->getAncestor($i);
    }

    /**
     * @return HtmlLayoutChange[]
     */
    public function getHtmlLayoutChanges(): array
    {
        return $this->htmlLayoutChanges;
    }
}
