<?php declare(strict_types=1);

namespace DaisyDiff\RangeDifferencer;

class LinkedRangeDifference extends RangeDifference
{
    const INSERT = 0;
    const DELETE = 1;

    /** @var LinkedRangeDifference */
    private $fNext;

    /**
     * @param  LinkedRangeDifference $next
     * @param  int                   $operation
     */
    public function __construct(?LinkedRangeDifference $next, int $operation = 1)
    {
        parent::__construct($operation);
        $this->fNext = $next;
    }

    public function getNext(): ?LinkedRangeDifference
    {
        return $this->fNext;
    }

    public function isDelete(): bool
    {
        return $this->kind() == self::DELETE;
    }

    public function isInsert(): bool
    {
        return $this->kind() == self::INSERT;
    }

    public function setNext(?RangeDifference $next): void
    {
        $this->fNext = $next;
    }
}
