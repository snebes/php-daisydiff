<?php declare(strict_types=1);

namespace DaisyDiff\Html\Ancestor;

use DaisyDiff\Html\Modification\HtmlLayoutChange;

/**
 * AncestorComparatorResult model.
 */
final class AncestorComparatorResult
{
    /** @var bool */
    private $changed = false;

    /** @var string */
    private $changes;

    /** @var HtmlLayoutChange[] */
    private $htmlLayoutChanges = [];

    /**
     * @return bool
     */
    public function isChanged(): bool
    {
        return $this->changed;
    }

    /**
     * @param  bool $value
     * @return self
     */
    public function setChanged(bool $value): self
    {
        $this->changed = $value;

        return $this;
    }

    /**
     * @return string
     */
    public function getChanges(): ?string
    {
        return $this->changes;
    }

    /**
     * @param  string|null $value
     * @return self
     */
    public function setChanges(?string $value): self
    {
        $this->changes = $value;

        return $this;
    }

    /**
     * @return HtmlLayoutChange[]
     */
    public function getHtmlLayoutChanges(): iterable
    {
        return $this->htmlLayoutChanges;
    }

    /**
     * @param  HtmlLayoutChange[] $value
     * @return self
     */
    public function setHtmlLayoutChanges(?array $value): self
    {
        $this->htmlLayoutChanges = $value ?? [];

        return $this;
    }
}
