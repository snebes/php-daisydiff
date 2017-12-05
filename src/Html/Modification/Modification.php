<?php declare(strict_types=1);

namespace DaisyDiff\Html\Modification;

/**
 * Modification model.
 */
class Modification
{
    /** @var ModificationType */
    private $type;

    /** @var ModificationType */
    private $outputType;

    /** @var int */
    private $id = -1;

    /** @var Modification */
    private $prevMod;

    /** @var Modification */
    private $nextMod;

    /** @var bool */
    private $firstOfId = false;

    /** @var array */
    private $htmlLayoutChanges;

    /**
     * @param  ModificationType $type
     * @param  ModificationType $outputType
     */
    public function __construct(string $type, string $outputType)
    {
        $this->type       = $type;
        $this->outputType = $outputType;
    }

    /**
     * @return ModificationType
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param  int $id
     * @return self
     */
    public function setId(?int $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return bool
     */
    public function isFirstOfId(): bool
    {
        return $this->firstOfId;
    }

    /**
     * @param  bool $value
     * @return self
     */
    public function setFirstOfId(bool $value): self
    {
        $this->firstOfId = $value;

        return $this;
    }

    /**
     * @return string
     */
    public function getChanges(): string
    {
        return $this->changes;
    }

    /**
     * @param  string $changes
     * @return self
     */
    public function setChanges(?string $changes): self
    {
        $this->changes = $changes;

        return $this;
    }

    /**
     * @return Modification
     */
    public function getPrevious(): ?Modification
    {
        return $this->prevMod;
    }

    /**
     * @param  Modification $mod
     * @return self
     */
    public function setPrevious(?Modification $mod): self
    {
        $this->prevMod = $mod;

        return $this;
    }

    /**
     * @return Modification
     */
    public function getNext(): ?Modification
    {
        return $this->nextMod;
    }

    /**
     * @param  Modification $mod
     * @return self
     */
    public function setNext(?Modification $mod): self
    {
        $this->nextMod = $mod;

        return $this;
    }
}
