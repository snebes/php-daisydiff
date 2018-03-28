<?php declare(strict_types=1);

namespace DaisyDiff\Html;

use DaisyDiff\Html\Ancestor\ChangeText;
use DaisyDiff\Xml\ContentHandlerInterface;
use DaisyDiff\Xml\Xml;

/**
 * Delegates content handling to ChangeText.
 */
class DelegatingContentHandler implements ContentHandlerInterface
{
    /** @var ChangeText */
    private $changeText;

    /**
     * @param ChangeText $changeText
     */
    public function __construct(ChangeText $changeText)
    {
        $this->changeText = $changeText;
    }

    /** {@inheritdoc} */
    public function startElement(string $qName, array $attributes = []): void
    {
        $this->changeText->addHtml(Xml::openElement($qName, $attributes));
    }

    /** {@inheritdoc} */
    public function endElement(string $qName): void
    {
        $this->changeText->addHtml(Xml::closeElement($qName));
    }

    /** {@inheritdoc} */
    public function characters(string $chars): void
    {
        $this->changeText->addHtml($chars);
    }
}
