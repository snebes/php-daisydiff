<?php

declare(strict_types=1);

namespace DaisyDiff\RangeDifferencer;

/**
 * Singly Linked RangeDifference
 */
class LinkedRangeDifference extends RangeDifference
{
    /** @const int */
    const INSERT = 0;
    const DELETE = 1;

    /** @var LinkedRangeDifference */
    private $next;

    /**
     * @param LinkedRangeDifference $next
     * @param int                   $operation
     */
    public function __construct(?LinkedRangeDifference $next, int $operation = RangeDifference::ERROR)
    {
        parent::__construct($operation);
        $this->next = $next;
    }

    /**
     * @return LinkedRangeDifference|null
     */
    public function getNext(): ?LinkedRangeDifference
    {
        return $this->next;
    }

    /**
     * @return bool
     */
    public function isDelete(): bool
    {
        return $this->kind() == self::DELETE;
    }

    /**
     * @return bool
     */
    public function isInsert(): bool
    {
        return $this->kind() == self::INSERT;
    }

    /**
     * @param RangeDifference|null $next
     */
    public function setNext(?RangeDifference $next): void
    {
        $this->next = $next;
    }
}
