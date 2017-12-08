<?php declare(strict_types=1);

namespace DaisyDiff\Html\Dom;

use RuntimeException;

/**
 * DOM Tree Builder.
 */
final class DomTreeBuilder
{
    /** @var TextNode[] */
    private $textNodes = [];

    /** @var BodyNode */
    private $bodyNode;

    /** @var TagNode */
    private $currentParent;

    /** @var string */
    private $newWord = '';

    /** @var bool */
    private $documentStarted = false;

    /** @var bool */
    private $documentEnded = false;

    /** @var bool */
    private $bodyStarted = false;

    /** @var bool */
    private $bodyEnded = false;

    /** @var bool */
    private $whiteSpaceBeforeThis = false;

    /** @var int */
    private $numberOfActivePreTags = 0;

    /** @var Node */
    private $lastSibling;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->bodyNode = new BodyNode();
        $this->currentParent = $this->bodyNode;
    }

    /**
     * @return BodyNode
     */
    public function getBodyNode(): BodyNode
    {
        return $this->bodyNode;
    }

    /**
     * @return TextNode[]
     */
    public function getTextNodes(): iterable
    {
        return $this->textNodes ?? [];
    }

    /**
     * @return bool
     */
    public function isDocumentStarted(): bool
    {
        return $this->documentStarted;
    }

    /**
     * @return void
     */
    public function startDocument(): void
    {
        if ($this->documentStarted) {
            throw new RuntimeException('This Handler only accepts one document.', 8000);
        }

        $this->documentStarted = true;
    }

    /**
     * @return bool
     */
    public function isDocumentEnded(): bool
    {
        return $this->documentEnded;
    }

    /**
     * @return void
     */
    public function endDocument(): void
    {
        if (!$this->documentStarted || $this->documentEnded) {
            throw new RuntimeException('No document opened.', 8001);
        }

        $this->documentEnded = true;
        $this->documentStarted = false;
    }

    /**
     * @param  resource $xmlParser
     * @param  string   $qName
     * @param  iterable $attributes
     * @return void
     */
    public function startElement($xmlParser, string $qName, ?iterable $attributes): void
    {
        if (!$this->documentStarted || $this->documentEnded) {
            throw new RuntimeException('No document is opened.', 8002);
        }

        if ($this->bodyStarted && !$this->bodyEnded) {
            $this->endWord();

            $newTagNode = new TagNode($this->currentParent, $qName, $attributes);
            $this->currentParent = $newTagNode;
            $this->lastSibling   = null;

            if ($this->whiteSpaceBeforeThis && $newTagNode->isInline()) {
                $newTagNode->setWhiteBefore(true);
            }

            $this->whiteSpaceBeforeThis = false;

            if ($newTagNode->isPre()) {
                $this->numberOfActivePreTags++;
            }

            if ($this->isSeparatingTag($newTagNode)) {
                $this->addSeparatorNode();
            }
        }
        elseif ($this->bodyStarted) {
            // Ignoring element after body tag closed.
        }
        elseif (0 === strcasecmp($qName, 'body')) {
            $this->bodyStarted = true;
        }
    }

    /**
     * @param  resource $xmlParser
     * @param  string   $qName
     * @return void
     */
    public function endElement($xmlParser, string $qName): void
    {
        if (!$this->documentStarted || $this->documentEnded) {
            throw new RuntimeException('No document is opened.', 8003);
        }

        if (0 === strcasecmp($qName, 'body')) {
            $this->bodyEnded = true;
        }
        elseif ($this->bodyStarted && !$this->bodyEnded) {
            if (0 === strcasecmp($qName, 'img')) {
                // Insert a dummy leaf for the image.
                $img = new ImageNode($this->currentParent, $this->currentParent->getAttributes());
                $img->setWhiteBefore($this->whiteSpaceBeforeThis);
                $this->lastSibling = $img;
                $this->textNodes[] = $img;
            }

            $this->endWord();

            if ($this->currentParent->isInline()) {
                $this->lastSibling = $this->currentParent;
            } else {
                $this->lastSibling = null;
            }

            if (0 === strcasecmp($qName, 'pre')) {
                $this->numberOfActivePreTags--;
            }

            if ($this->isSeparatingTag($this->currentParent)) {
                $this->addSeparatorNode();
            }

            $this->currentParent = $this->currentParent->getParent();
            $this->whiteSpaceBeforeThis = false;
        }
    }

    /**
     * @param  resource $xmlParser
     * @param  string   $chars
     * @return void
     */
    public function characters($xmlParser, string $chars): void
    {
        if (!$this->documentStarted || $this->documentEnded) {
            throw new RuntimeException('No document is opened.', 8004);
        }

        for ($i = 0, $max = mb_strlen($chars); $i < $max; $i++) {
            $c = $chars[$i];

            if ($this->isDelimiter($c)) {
                $this->endWord();

                if (WhiteSpaceNode::isWhiteSpace($c) && $this->numberOfActivePreTags == 0) {
                    if (!is_null($this->lastSibling)) {
                        $this->lastSibling->setWhiteAfter(true);
                    }

                    $this->whiteSpaceBeforeThis = true;
                } else {
                    $textNode = new TextNode($this->currentParent, $c);
                    $textNode->setWhiteBefore(false);

                    $this->whiteSpaceBeforeThis = false;
                    $this->lastSibling = $textNode;
                    $this->textNodes[] = $textNode;
                }
            } else {
                $this->newWord .= $c;
            }
        }
    }

    /**
     * @return void
     */
    private function endWord(): void
    {
        if (mb_strlen($this->newWord) > 0) {
            $node = new TextNode($this->currentParent, $this->newWord);
            $node->setWhiteBefore($this->whiteSpaceBeforeThis);

            $this->whiteSpaceBeforeThis = false;
            $this->lastSibling = $node;
            $this->textNodes[] = $node;
            $this->newWord = '';
        }
    }

    /**
     * Returns true if the given tag separates text nodes from being successive. I.e. every block starts a new distinct
     * text flow.
     *
     * @param  TagNode $tagNode
     * @return bool
     */
    private function isSeparatingTag(TagNode $tagNode): bool
    {
        return $tagNode->isBlockLevel();
    }

    /**
     * Ensures that a separator is added after the last text node.
     *
     * @return void
     */
    private function addSeparatorNode(): void
    {
        if (empty($this->textNodes)) {
            return;
        }

        // Don't add multiple separators.
        $count = count($this->textNodes);

        if ($this->textNodes[$count - 1] instanceof SeparatingNode) {
            return;
        }

        $this->textNodes[] = new SeparatingNode($this->currentParent);
    }

    /**
     * @return bool
     */
    public static function isDelimiter(string $c): bool
    {
        if (WhiteSpaceNode::isWhiteSpace($c)) {
            return true;
        }

        switch ($c) {
            case '/':
            case '.':
            case '!':
            case ',':
            case ';':
            case '?':
            case '=':
            case '\'':
            case '"':
            case '[':
            case ']':
            case '{':
            case '}':
            case '(':
            case ')':
            case '&':
            case '|':
            case "\\":
            case '-':
            case '_':
            case '+':
            case '*':
            case ':':
                return true;
            default:
                return false;
        }
    }
}
