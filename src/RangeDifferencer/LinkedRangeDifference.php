<?php
/**
 * (c) Steve Nebes <snebes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
     * @param LinkedRangeDifference|null $next
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
        return $this->getKind() === self::DELETE;
    }

    /**
     * @return bool
     */
    public function isInsert(): bool
    {
        return $this->getKind() === self::INSERT;
    }

    /**
     * @param LinkedRangeDifference|null $next
     */
    public function setNext(?LinkedRangeDifference $next): void
    {
        $this->next = $next;
    }
}
