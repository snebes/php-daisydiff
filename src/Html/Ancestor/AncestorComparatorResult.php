<?php
/**
 * (c) Steve Nebes <snebes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace SN\DaisyDiff\Html\Ancestor;

use SN\DaisyDiff\Html\Modification\HtmlLayoutChange;

/**
 * AncestorComparatorResult model.
 */
class AncestorComparatorResult
{
    /** @var bool */
    private $changed = false;

    /** @var string */
    private $changes = '';

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
     * @param bool $value
     */
    public function setChanged(bool $value): void
    {
        $this->changed = $value;
    }

    /**
     * @return string
     */
    public function getChanges(): string
    {
        return $this->changes;
    }

    /**
     * @param string|null $value
     */
    public function setChanges(?string $value): void
    {
        $this->changes = $value ?? '';
    }

    /**
     * @return HtmlLayoutChange[]
     */
    public function getHtmlLayoutChanges(): array
    {
        return $this->htmlLayoutChanges;
    }

    /**
     * @param HtmlLayoutChange[] $value
     */
    public function setHtmlLayoutChanges(?array $value): void
    {
        $this->htmlLayoutChanges = $value ?? [];
    }
}
