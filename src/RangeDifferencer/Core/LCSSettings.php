<?php
/**
 * (c) Steve Nebes <snebes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace SN\DaisyDiff\RangeDifferencer\Core;

/**
 * Longest Common Subsequence Settings.
 */
class LCSSettings
{
    /** @var float */
    private $tooLong = 10000000.0;

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
     * @param float $value
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
     * @param float $value
     */
    public function setPowLimit(float $value): void
    {
        $this->powLimit = $value;
    }

    /**
     * @return bool
     */
    public function isUseGreedyMethod(): bool
    {
        return $this->useGreedyMethod;
    }

    /**
     * @param bool $value
     */
    public function setUseGreedyMethod(bool $value): void
    {
        $this->useGreedyMethod = $value;
    }
}
