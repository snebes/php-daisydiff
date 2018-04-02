<?php declare(strict_types=1);

namespace DaisyDiff\Html\Dom;

use DaisyDiff\Html\Modification\Modification;
use DaisyDiff\Html\Modification\ModificationType;

/**
 * Represents a piece of text in the HTML file.
 */
class TextNode extends Node
{
    /** @var string */
    private $s = '';

    /** @var Modification */
    private $modification;

    /**
     * @param TagNode $parent
     * @param string  $s
     */
    public function __construct(?TagNode $parent, string $s)
    {
        parent::__construct($parent);

        $this->s = $s;
        $this->modification = new Modification(ModificationType::NONE, ModificationType::NONE);
    }

    /**
     * {@inheritdoc}
     */
    public function copyTree(): Node
    {
        $node = clone $this;
        $node->setParent(null);

        return $node;
    }

    /**
     * {@inheritdoc}
     */
    public function getLeftMostChild(): Node
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

        if (null !== $modification && $modification->getType() == ModificationType::REMOVED &&
            $modification->getId() == $id) {
            $nodes = [$this];
        }

        return $nodes;
    }

    /**
     * {@inheritdoc}
     */
    public function getRightMostChild(): Node
    {
        return $this;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->s;
    }

    /**
     * @param  Node $other
     * @return bool
     */
    public function isSameText(?Node $other): bool
    {
        if (null === $other || !$other instanceof TextNode) {
            return false;
        }

        return 0 == strcmp($this->getText(), $other->getText());
    }

    /**
     * @return Modification
     */
    public function getModification(): ?Modification
    {
        return $this->modification;
    }

    /**
     * @param  Modification $m
     * @return self
     */
    public function setModification(?Modification $m): self
    {
        $this->modification = $m;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->getText();
    }
}
