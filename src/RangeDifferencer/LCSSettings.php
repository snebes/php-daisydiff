<?php declare(strict_types=1);

namespace DaisyDiff\RangeDifferencer;

/**
 * Least Common Sequence Settings.
 */
final class LCSSettings
{
    /** @var float */
    private $tooLong = 100000000.0;

    /** @var float */
    private $powLimit = 1.5;

    /** @var bool */
    private $useGreedyMethod = false;

    /**
     * @return float
     */
    public function getTooLong(): float
    {
        return $this->tooLong;
    }

    /**
     * @param  float $value
     * @return void
     */
    public function setTooLong(float $value): void
    {
        $this->tooLong = $value;
    }

    /**
     * @return float
     */
    public function getPowLimit(): float
    {
        return $this->powLimit;
    }

    /**
     * @param  float $value
     * @return void
     */
    public function setPowLimit(float $value): void
    {
        $this->powLimit = $value;
    }

    /**
     * @return bool
     */
    public function useGreedyMethod(): bool
    {
        return $this->useGreedyMethod;
    }

    /**
     * @param  bool $value
     * @return void
     */
    public function setGreedyMethod(bool $value): void
    {
        $this->useGreedyMethod = $value;
    }
}
