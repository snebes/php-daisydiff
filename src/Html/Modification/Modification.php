<?php
/**
 * (c) Steve Nebes <snebes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace DaisyDiff\Html\Modification;

/**
 * Modification model.
 */
class Modification
{
    /** @var string */
    private $type;

    /** @var string */
    private $outputType;

    /** @var int */
    private $id = -1;

    /** @var Modification */
    private $prevMod;

    /** @var Modification */
    private $nextMod;

    /** @var bool */
    private $firstOfId = false;

    /** @var string */
    private $changes = '';

    /** @var HtmlLayoutChange[] */
    private $htmlLayoutChanges = [];

    /**
     * @param string $type
     * @param string $outputType
     */
    public function __construct(string $type, string $outputType)
    {
        $this->type = $type;
        $this->outputType = $outputType;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Returns the type of this modification regarding output formatting (i.e. in order to specify how this modification
     * shall be formatted).
     *
     * In three-way diffs we format "ADDED" modifications as REMOVED, and the other way round, because the comparison is
     * reversed, compared to a two-way diff.
     *
     * @return string
     */
    public function getOutputType(): string
    {
        return $this->outputType;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return Modification|null
     */
    public function getPrevious(): ?Modification
    {
        return $this->prevMod;
    }

    /**
     * @param Modification|null $mod
     */
    public function setPrevious(?Modification $mod): void
    {
        $this->prevMod = $mod;
    }

    /**
     * @return Modification|null
     */
    public function getNext(): ?Modification
    {
        return $this->nextMod;
    }

    /**
     * @param Modification|null $mod
     */
    public function setNext(?Modification $mod): void
    {
        $this->nextMod = $mod;
    }

    /**
     * @return string
     */
    public function getChanges(): string
    {
        return $this->changes;
    }

    /**
     * @param string $changes
     */
    public function setChanges(string $changes): void
    {
        $this->changes = $changes;
    }

    /**
     * @return bool
     */
    public function isFirstOfId(): bool
    {
        return $this->firstOfId;
    }

    /**
     * @param bool $value
     */
    public function setFirstOfId(bool $value): void
    {
        $this->firstOfId = $value;
    }

    /**
     * @return HtmlLayoutChange[]
     */
    public function getHtmlLayoutChanges(): array
    {
        return $this->htmlLayoutChanges;
    }

    /**
     * @param HtmlLayoutChange[] $htmlLayoutChanges
     */
    public function setHtmlLayoutChanges(array $htmlLayoutChanges): void
    {
        $this->htmlLayoutChanges = $htmlLayoutChanges;
    }
}
