<?php
/**
 * (c) Steve Nebes <snebes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace DaisyDiff\Html\Dom;

/**
 * Creates a DOM tree from SAX-like events.
 */
class DomTreeBuilder
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

    /** @var Node|null */
    private $lastSibling;

    /**
     * Default values.
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
    public function getTextNodes(): array
    {
        return $this->textNodes;
    }

    /**
     * @return bool
     */
    public function isDocumentStarted(): bool
    {
        return $this->documentStarted;
    }

    /**
     * @return bool
     */
    public function isDocumentEnded(): bool
    {
        return $this->documentEnded;
    }

    /**
     * Starts the document, if one has not already been started.
     *
     * @throws \RuntimeException
     */
    public function startDocument(): void
    {
        if ($this->documentStarted) {
            throw new \RuntimeException('This Handler only accepts one document.');
        }

        $this->documentStarted = true;
    }

    /**
     * Ends the document, if a document is started.
     *
     * @throws \RuntimeException
     */
    public function endDocument(): void
    {
        if (!$this->documentStarted || $this->documentEnded) {
            throw new \RuntimeException();
        }

        $this->endWord();

        $this->documentEnded = true;
        $this->documentStarted = false;
    }

    /**
     * @param mixed  $xmlParser
     * @param string $qName
     * @param array  $attributes
     *
     * @throws \RuntimeException
     */
    public function startElement($xmlParser, string $qName, array $attributes = []): void
    {
        // Required parameter, but not used.
        \assert($xmlParser);

        $qName = \mb_strtolower($qName);

        if (!$this->documentStarted || $this->documentEnded) {
            throw new \RuntimeException();
        }

        if ($this->bodyStarted && !$this->bodyEnded) {
            $this->endWord();

            $newTagNode = new TagNode($this->currentParent, $qName, $attributes);
            $this->currentParent = $newTagNode;
            $this->lastSibling = null;

            if ($this->whiteSpaceBeforeThis && $newTagNode->isInline()) {
                $this->currentParent->setWhiteBefore(true);
            }

            $this->whiteSpaceBeforeThis = false;

            if ($newTagNode->isPre()) {
                $this->numberOfActivePreTags++;
            }

            if ($this->isSeparatingTag($newTagNode)) {
                $this->addSeparatorNode();
            }
        } elseif ($this->bodyStarted) {
            // Ignoring element after body tag closed.
        } elseif ('body' === $qName) {
            $this->bodyStarted = true;
        }
    }

    /**
     * @param mixed  $xmlParser
     * @param string $qName
     *
     * @throws \RuntimeException
     */
    public function endElement($xmlParser, string $qName): void
    {
        // Required parameter, but not used.
        \assert($xmlParser);

        $qName = \mb_strtolower($qName);

        if (!$this->documentStarted || $this->documentEnded) {
            throw new \RuntimeException();
        }

        if ('body' === $qName) {
            $this->bodyEnded = true;
        } elseif ($this->bodyStarted && !$this->bodyEnded) {
            if ('img' === $qName) {
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

            if ('pre' === $qName) {
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
     * @param mixed  $xmlParser
     * @param string $chars
     *
     * @throws \RuntimeException
     */
    public function characters($xmlParser, string $chars): void
    {
        \assert($xmlParser);

        if (!$this->documentStarted || $this->documentEnded) {
            throw new \RuntimeException();
        }

        for ($i = 0, $iMax = \mb_strlen($chars); $i < $iMax; $i++) {
            $c = \mb_substr($chars, $i, 1);

            if ($this->isDelimiter($c)) {
                $this->endWord();

                if (WhiteSpaceNode::isWhiteSpace($c) && $this->numberOfActivePreTags === 0) {
                    if (null !== $this->lastSibling) {
                        $this->lastSibling->setWhiteAfter(true);
                    }

                    $this->whiteSpaceBeforeThis = true;
                } else {
                    $textNode = new TextNode($this->currentParent, $c);
                    $textNode->setWhiteBefore($this->whiteSpaceBeforeThis);

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
        if (\mb_strlen($this->newWord) > 0) {
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
     * @param TagNode $tagNode
     * @return bool
     */
    private function isSeparatingTag(TagNode $tagNode): bool
    {
        return $tagNode->isBlockLevel();
    }

    /**
     * Ensures that a separator is added after the last text node.
     */
    private function addSeparatorNode(): void
    {
        if (empty($this->textNodes)) {
            return;
        }

        // Don't add multiple separators.
        if ($this->textNodes[\count($this->textNodes) - 1] instanceof SeparatingNode) {
            return;
        }

        $this->textNodes[] = new SeparatingNode($this->currentParent);
    }

    /**
     * @param string $c
     * @return bool
     */
    public static function isDelimiter(string $c): bool
    {
        if (WhiteSpaceNode::isWhiteSpace($c)) {
            return true;
        }

        switch ($c) {
            // Basic Delimiters
            case '/':
            case '.':
            case '!':
            case ',':
            case ';':
            case '?':
            case '=':
            case "'":
            case '"':
                // Extra Delimiters
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
                break;
        }

        return false;
    }
}
