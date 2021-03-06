<?php
/**
 * (c) Steve Nebes <snebes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace SN\DaisyDiff\Html\Dom;

use SN\DaisyDiff\Html\Modification\Modification;
use SN\DaisyDiff\Html\Modification\ModificationType;

/**
 * Represents a piece of text in the HTML file.
 */
class TextNode extends Node
{
    /** @var string */
    private $s = '';

    /** @var Modification|null */
    private $modification;

    /**
     * @param TagNode $parent
     * @param string  $s
     */
    public function __construct(?TagNode $parent, string $s)
    {
        parent::__construct($parent);

        $this->modification = new Modification(ModificationType::NONE, ModificationType::NONE);
        $this->s = $s;
    }

    /**
     * @todo Check if we need to deep clone this.
     *
     * @return Node
     */
    public function copyTree(): Node
    {
        $node = clone $this;
        $node->setParent(null);

        return $node;
    }

    /**
     * @return Node
     */
    public function getLeftMostChild(): Node
    {
        return $this;
    }

    /**
     * @return Node
     */
    public function getRightMostChild(): Node
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getMinimalDeletedSet(int $id): array
    {
        $nodes = [];
        $modification = $this->getModification();

        if ($modification->getType() === ModificationType::REMOVED && $modification->getId() === $id) {
            $nodes[] = $this;
        }

        return $nodes;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->s;
    }

    /**
     * @param Node|null $other
     * @return bool
     */
    public function isSameText(?Node $other): bool
    {
        if ($other instanceof TextNode) {
            return $this->getText() === $other->getText();
        }

        return false;
    }

    /**
     * @return Modification|null
     */
    public function getModification(): ?Modification
    {
        return $this->modification;
    }

    /**
     * @param Modification|null $m
     */
    public function setModification(?Modification $m): void
    {
        $this->modification = $m;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->getText();
    }
}
