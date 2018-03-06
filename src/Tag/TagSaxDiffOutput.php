<?php

namespace DaisyDiff\Tag;

use DaisyDiff\Output\TextDiffOutputInterface;
use DaisyDiff\Xml\AttributeBag;
use DaisyDiff\Xml\ContentHandlerInterface;

/**
 * Outputs the diff result as HTML elements to a SAX ContentHandler. The startDocument and endDocument events are not
 * generated by this class. This version is used for tag based diff results.
 */
class TagSaxDiffOutput implements TextDiffOutputInterface
{
    /** @var ContentHandlerInterface */
    private $consumer;

    /** @var bool */
    private $insideTag = false;

    /** @var int */
    private $removedId = 1;

    /** @var int */
    private $addedId = 1;

    /**
     * @param ContentHandlerInterface $consumer
     */
    public function __construct(ContentHandlerInterface $consumer)
    {
        $this->consumer = $consumer;
    }

    /**
     * @param string $text
     */
    public function addClearPart(string $text): void
    {
        $this->addBasicText($text);
    }

    private function addBasicText(string $text): void
    {
        $c = str_split($text);

        for ($i = 0; $i < count($c); $i++) {
            switch ($c[$i]) {
                case "\n":
                    $this->consumer->startElement('br');
                    $this->consumer->endElement('br');
                    $this->consumer->characters("\n");
                    break;

                case '<':
                    if (false === $this->insideTag) {
                        $attrs = new AttributeBag([
                            'class' => 'diff-tag-html',
                        ]);
                        $this->consumer->startElement('span', $attrs);
                        $this->insideTag = true;
                    } else {
                        $this->consumer->endElement('span');
                        $this->insideTag = false;
                    }

                    $this->consumer->characters('<');
                    break;

                case '>':
                    $this->consumer->characters('>');

                    if (true === $this->insideTag) {
                        $this->consumer->endElement('span');
                        $this->insideTag = false;
                    }
                    break;

                default:
                    $this->consumer->characters($c[$i]);
                    break;
            }
        }
    }

    /**
     * @param string $text
     */
    public function addRemovedPart(string $text): void
    {
        $attrs = new AttributeBag([
            'class' => 'diff-tag-removed',
            'id'    => 'removed' . $this->removedId,
            'title' => '#removed' . $this->removedId,
        ]);
        $this->removedId++;

        $this->consumer->startElement('span', $attrs);
        $this->addBasicText($text);
        $this->consumer->endElement('span');
    }

    /**
     * @param string $text
     */
    public function addAddedPart(string $text): void
    {
        $attrs = new AttributeBag([
            'class' => 'diff-tag-added',
            'id'    => 'removed' . $this->addedId,
            'title' => '#removed' . $this->addedId,
        ]);
        $this->addedId++;

        $this->consumer->startElement('span', $attrs);
        $this->addBasicText($text);
        $this->consumer->endElement('span');
    }
}
