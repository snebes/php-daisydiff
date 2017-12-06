<?php declare(strict_types=1);

namespace DaisyDiff\RangeDifferencer;

class LinkedRangeDifference extends RangeDifference
{
    /** @var LinkedRangeDifference */
    private $fNext;

    /**
     */
    public function __construct(?LinkedRangeDifference $next, int $operation = 0)
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
        return $this->kind() == RangeDifferenceType::DELETE;
    }

    public function isInsert(): bool
    {
        return $this->kind() == RangeDifferenceType::INSERT;
    }

    public function setNext(?RangeDifference $next): void
    {
        $this->fNext = $next;
    }
}
