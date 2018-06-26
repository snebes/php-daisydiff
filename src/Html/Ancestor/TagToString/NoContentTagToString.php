<?php

declare(strict_types=1);

namespace DaisyDiff\Html\Ancestor\TagToString;

use DaisyDiff\Html\Ancestor\ChangeText;

/**
 * Image <img> tag to string.
 */
class NoContentTagToString extends TagToString
{
    /**
     * @param  ChangeText $text
     * @return void
     */
    public function getAddedDescription(ChangeText $text): void
    {
        $text->addText(sprintf('%s %s ', $this->getChangedTo(), mb_strtolower($this->getArticle())));
        $text->addHtml('<b>');
        $text->addText(mb_strtolower($this->getDescription()));
        $text->addHtml('</b>');

        $this->addAttributes($text, $this->node->getAttributes());
        $text->addText('.');
    }

    /**
     * @return string
     */
    private function getChangedTo(): string
    {
        return $this->getString('diff-changedto');
    }

    /**
     * @param  ChangeText $text
     * @return void
     */
    public function getRemovedDescription(ChangeText $text): void
    {
        $text->addText(sprintf('%s %s ', $this->getChangedFrom(), mb_strtolower($this->getArticle())));
        $text->addHtml('<b>');
        $text->addText(mb_strtolower($this->getDescription()));
        $text->addHtml('</b>');

        $this->addAttributes($text, $this->node->getAttributes());
        $text->addText('.');
    }

    /**
     * @return string
     */
    private function getChangedFrom(): string
    {
        return $this->getString('diff-changedto');
    }
}
