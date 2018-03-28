<?php declare(strict_types=1);

namespace DaisyDiff\Html\Dom;

/**
 * DomTree interface.
 */
interface DomTreeInterface
{
    /**
     * @return TextNode[]
     */
    public function getTextNodes(): array;

    /**
     * @return BodyNode
     */
    public function getBodyNode(): BodyNode;
}
