<?php
/**
 * (c) Steve Nebes <snebes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace DaisyDiff\Html\Ancestor;

use DaisyDiff\Html\Ancestor\TagToString\TagToStringFactory;
use DaisyDiff\Html\ChangeText;
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

    /**
     * @param AncestorComparator $ancestorComparator
     * @param AncestorComparator $other
     */
    public function __construct(AncestorComparator $ancestorComparator, AncestorComparator $other)
    {
        $this->ancestorComparator = $ancestorComparator;
        $this->other = $other;
        $this->factory = new TagToStringFactory();
    }

    /**
     * @param RangeDifference[] $differences
     * @return ChangeText
     */
    public function getChanged(array $differences): ChangeText
    {
        $text = new ChangeText();
        $rootListOpened = false;

        if (\count($differences) > 1) {
            $text->startElement('ul', ['class' => 'changelist']);
            $rootListOpened = true;
        }

        for ($j = 0, $jMax = \count($differences); $j < $jMax; $j++) {
            /** @var RangeDifference $d */
            $d = $differences[$j];
            $lvl1ListOpened = false;

            if ($rootListOpened) {
                $text->startElement('li');
            }

            if ($d->getLeftLength() + $d->getRightLength() > 1) {
                $text->startElement('ul', ['class' => 'changelist']);
                $lvl1ListOpened = true;
            }

            // Left are the old ones.
            for ($i = $d->getLeftStart(), $iMax = $d->getLeftEnd(); $i < $iMax; $i++) {
                if ($lvl1ListOpened) {
                    $text->startElement('li');
                }

                // Add a bullet for an old tag.
                $this->addTagOld($text, $this->other->getAncestor($i));

                if ($lvl1ListOpened) {
                    $text->endElement('li');
                }
            }

            // Right are the new ones.
            for ($i = $d->getRightStart(), $iMax = $d->getRightEnd(); $i < $iMax; $i++) {
                if ($lvl1ListOpened) {
                    $text->startElement('li');
                }

                // Add a bullet for an old tag.
                $this->addTagNew($text, $this->getAncestor($i));

                if ($lvl1ListOpened) {
                    $text->endElement('li');
                }
            }

            if ($lvl1ListOpened) {
                $text->endElement('ul');
            }

            if ($rootListOpened) {
                $text->endElement('li');
            }
        }

        if ($rootListOpened) {
            $text->endElement('ul');
        }

        return $text;
    }

    /**
     * @param ChangeText $text
     * @param TagNode    $ancestor
     */
    private function addTagOld(ChangeText $text, TagNode $ancestor): void
    {
        $tagToString = $this->factory->create($ancestor);
        $tagToString->getRemovedDescription($text);
        $this->htmlLayoutChanges[] = $tagToString->getHtmlLayoutChange();
    }

    /**
     * @param ChangeText $text
     * @param TagNode    $ancestor
     */
    private function addTagNew(ChangeText $text, TagNode $ancestor): void
    {
        $tagToString = $this->factory->create($ancestor);
        $tagToString->getAddedDescription($text);
        $this->htmlLayoutChanges[] = $tagToString->getHtmlLayoutChange();
    }

    /**
     * @param int $index
     * @return TagNode
     */
    private function getAncestor(int $index): TagNode
    {
        return $this->ancestorComparator->getAncestor($index);
    }

    /**
     * @return HtmlLayoutChange[]
     */
    public function getHtmlLayoutChanges(): array
    {
        return $this->htmlLayoutChanges;
    }
}
