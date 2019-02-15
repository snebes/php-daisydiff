<?php
/**
 * (c) Steve Nebes <snebes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace SN\DaisyDiff;

use SN\DaisyDiff\Node\Cursor;
use SN\DaisyDiff\Node\DocumentNode;
use SN\DaisyDiff\Node\TextNode;
use SN\DaisyDiff\Visitor\NodeVisitorInterface;

/**
 * The DomVisitor iterates over the parsed DOM tree and visits nodes using NodeVisitorInterface objects.
 */
class DomVisitor implements DomVisitorInterface
{
    /** @var NodeVisitorInterface[] */
    private $nodeVisitors = [];

    /** @var DocumentNode */
    private $documentNode;

    /** @var TextNode[] */
    private $textNodes = [];

    /**
     * Default values.
     *
     * @param NodeVisitorInterface[] $nodeVisitors
     */
    public function __construct(array $nodeVisitors = [])
    {
        foreach ($nodeVisitors as $nodeVisitor) {
            foreach ($nodeVisitor->getSupportedNodeNodes() as $nodeName) {
                $this->nodeVisitors[$nodeName][] = $nodeVisitor;
            }
        }
    }

    /**
     * @param \DOMNode $node
     * @return DocumentNode
     */
    public function visit(\DOMNode $node): DocumentNode
    {
        $cursor = new Cursor();
        $cursor->node = new DocumentNode();
        $this->documentNode = $cursor->node;

        $this->visitNode($node, $cursor);

        return $cursor->node;
    }

    /**
     * @param \DOMNode $node
     * @param Cursor   $cursor
     */
    private function visitNode(\DOMNode $node, Cursor $cursor): void
    {
        foreach ($this->nodeVisitors as $nodeVisitor) {
            if ($nodeVisitor->supports($node, $cursor)) {
                $nodeVisitor->enterNode($node, $cursor);
            }
        }

        /** @var \DOMNode $child */
        foreach ($node->childNodes ?? [] as $child) {
            if ('#text' === $child->nodeName) {
                // Add text in the tree without a visitor.
                $tokens = \preg_split('/([\s]+)/', $child->nodeValue, -1, \PREG_SPLIT_NO_EMPTY | \PREG_SPLIT_DELIM_CAPTURE);

                if (false !== $tokens) {
                    foreach ($tokens as $token) {
                        $cursor->node->addChild(new TextNode($cursor->node, $token));
                    }
                }
            } elseif (!$child instanceof \DOMText) {
                // Ignore comments.
                $this->visitNode($child, $cursor);
            }
        }

        foreach (\array_reverse($this->nodeVisitors) as $nodeVisitor) {
            if ($nodeVisitor->supports($node, $cursor)) {
                $nodeVisitor->leaveNode($node, $cursor);
            }
        }
    }
}
