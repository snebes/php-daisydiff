<?php declare(strict_types=1);

namespace DaisyDiff\RangeDifferencer\Core;

/**
 * LCS - Longest Common Sequence - Common Methods.
 *
 * Used to determine the change set responsible for each line.
 */
abstract class LCS
{
    /** @const float */
    const TOO_LONG = 100000000.0;

    /** @const float */
    const POW_LIMIT = 1.5;

    /** @var int */
    private $maxDifferences = 0;

    /** @var int */
    private $length = 0;

    /**
     * @return int
     */
    public function getLength(): int
    {
        return $this->length;
    }

    /**
     * Myers' algorithm for longest common subsequence. O((M + N)D) worst case time, O(M + N + D^2) expected time,
     * O(M + N) space (http://citeseer.ist.psu.edu/myers86ond.html)
     *
     * Note: Beyond implementing the algorithm as described in the paper I have added diagonal range compression which
     * helps when finding the LCS of a very long and a very short sequence, also bound the running time to (N + M)^1.5
     * when both sequences are very long.
     *
     * After this method is called, the longest common subsequence is available by calling getResult() where result[0]
     * is composed of entries from l1 and result[1] is composed of entries from l2
     *
     * @return void
     */
    public function longestCommonSubsequence(): void
    {
        $length1 = $this->getLength1();
        $length2 = $this->getLength2();

        if (0 == $length1 || 0 == $length2) {
            $this->length = 0;
            return;
        }

        $this->maxDifferences = intval(ceil(($length1 + $length2 + 1) / 2.0)); // ceil((N+M)/2);

        if (floatval($length1 * $length2) > self::TOO_LONG) {
            // Limit complexity to D^POW_LIMIT for long sequences
            $this->maxDifferences = intval(pow($this->maxDifferences, self::POW_LIMIT - 1.0));
        }

        $this->initializeLcs($length1);

        // The common prefixes and suffixes are always part of some LCS, include them now to reduce our search space.
        $forwardBound = 0;
        $max = min($length1, $length2);

        for (; $forwardBound < $max && $this->isRangeEqual($forwardBound, $forwardBound); $forwardBound++) {
            $this->setLcs($forwardBound, $forwardBound);
        }

        $backBoundL1 = $length1 - 1;
        $backBoundL2 = $length2 - 1;

        while ($backBoundL1 >= $forwardBound && $backBoundL2 >= $forwardBound &&
                $this->isRangeEqual($backBoundL1, $backBoundL2)) {
            $this->setLcs($backBoundL1, $backBoundL2);
            $backBoundL1--;
            $backBoundL2--;
        }

        $V     = array_fill(0, 2, array_fill(0, $length1 + $length2 + 1, 0));
        $snake = array_fill(0, 3, 0);

        $lcsRec = $this->lcsRec($forwardBound, $backBoundL1, $forwardBound, $backBoundL2, $V, $snake);
        $this->length = $forwardBound + $length1 - $backBoundL1 - 1 + $lcsRec;
    }

    /**
     * The recursive helper function for Myers' LCS. Computes the LCS of l1[bottoml1 .. topl1] and l2[bottoml2 .. topl2]
     * fills in the appropriate location in lcs and returns the length.
     *
     * @param  int     $bottomL1
     * @param  int     $topL1
     * @param  int     $bottomL2
     * @param  int     $topL2
     * @param  int[][] $V
     * @param  int[]   $snake
     * @return int
     */
    private function lcsRec(int $bottomL1, int $topL1, int $bottomL2, int $topL2, array &$V, array &$snake): int
    {
        // Check that both sequences are non-empty.
        if ($bottomL1 > $topL1 || $bottomL2 > $topL2) {
            return 0;
        }

        /** @var int */
        $d = $this->findMiddleSnake($bottomL1, $topL1, $bottomL2, $topL2, $V, $snake);

        // Need to store these so we don't lose them when they're overwritten by the recursion.
        $len    = $snake[2];
        $startx = $snake[0];
        $starty = $snake[1];

        // The middle snake is part of the LCS, store it.
        for ($i = 0; $i < $len; $i++) {
            $this->setLcs($startx + $i, $starty + $i);
        }

        if ($d > 1) {
            $lcs1 = $this->lcsRec($bottomL1, $startx - 1, $bottomL2, $starty - 1, $V, $snake);
            $lcs2 = $this->lcsRec($startx + $len, $topL1, $starty + $len, $topL2, $V, $snake);

            return $len + $lcs1 + $lcs2;
        }
        elseif ($d == 1) {
            // In this case the sequences differ by exactly 1 line. We have already saved all the lines after the
            // difference in the for loop above, now we need to save all the lines before the difference.
            $max = min($startx - $bottomL1, $starty - $bottomL2);

            for ($i = 0; $i < $max; $i++) {
                $this->setLcs($bottomL1 + $i, $bottomL2 + $i);
            }

            return $max + $len;
        }

        return $len;
    }

    /**
     * Helper function for Myers' LCS algorithm to find the middle snake for l1[bottoml1..topl1] and l2[bottoml2..topl2]
     * The x, y coodrdinates of the start of the middle snake are saved in snake[0], snake[1] respectively and the
     * length of the snake is saved in s[2].
     *
     * @param  int     $bottomL1
     * @param  int     $topL1
     * @param  int     $bottomL2
     * @param  int     $topL2
     * @param  int[][] $V
     * @param  int[]   $snake
     * @return int
     */
    private function findMiddleSnake(
        int $bottomL1,
        int $topL1,
        int $bottomL2,
        int $topL2,
        array &$V,
        array &$snake
    ): int {
        $N = $topL1 - $bottomL1 + 1;
        $M = $topL2 - $bottomL2 + 1;

        $delta  = $N - $M;
        $isEven = ($delta & 1) == 1? false : true;

        $limit = min($this->maxDifferences, intval(ceil(($N + $M + 1) / 2.0)));

        // Offset to make it odd/even.
        // a 0 or 1 that we add to the start offset to make it odd/even
        $valueToAddForward  = ($M & 1) == 1? 1 : 0;
        $valueToAddBackward = ($N & 1) == 1? 1 : 0;

        $startForward  = -$M;
        $endForward    = $N;
        $startBackward = -$N;
        $endBackward   = $M;

        $V[0][$limit + 1] = 0;
        $V[1][$limit - 1] = $N;

        for ($d = 0; $d <= $limit; $d++) {
            $startDiag = max($valueToAddForward + $startForward, -$d);
            $endDiag   = min($endForward, $d);
            $valueToAddForward = 1 - $valueToAddForward;

            // Compute forward furthest reaching paths.
            for ($k = $startDiag; $k <= $endDiag; $k += 2) {
                if ($k == -$d || ($k < $d && $V[0][$limit + $k - 1] < $V[0][$limit + $k + 1])) {
                    $x = $V[0][$limit + $k + 1];
                } else {
                    $x = $V[0][$limit + $k - 1] + 1;
                }

                $y = $x - $k;

                $snake[0] = $x + $bottomL1;
                $snake[1] = $y + $bottomL2;
                $snake[2] = 0;

                while ($x < $N && $y < $M && $this->isRangeEqual($x + $bottomL1, $y + $bottomL2)) {
                    $x++;
                    $y++;
                    $snake[2]++;
                }

                $V[0][$limit + $k] = $x;

                if (!$isEven && $k >= $delta - $d + 1 && $k <= $delta + $d - 1 && $x >= $V[1][$limit + $k - $delta]) {
                    return intval(2 * $d - 1);
                }

                // Check to see if we can cut down the diagonal range.
                if ($x >= $N && $endForward > $k - 1) {
                    $endForward = $k - 1;
                }
                elseif ($y >= $M) {
                    $startForward = $k + 1;
                    $valueToAddForward = 0;
                }
            }

            $startDiag = max($valueToAddBackward + $startBackward, -$d);
            $endDiag   = min($endBackward, $d);
            $valueToAddBackward = 1 - $valueToAddBackward;

            // Compute backward furthest reaching paths.
            for ($k = $startDiag; $k <= $endDiag; $k += 2) {
                if ($k == $d || ($k != -$d && $V[1][$limit + $k - 1] < $V[1][$limit + $k + 1])) {
                    $x = $V[1][$limit + $k - 1];
                } else {
                    $x = $V[1][$limit + $k + 1] - 1;
                }

                $y = $x - $k - $delta;
                $snake[2] = 0;

                while ($x > 0 && $y > 0 && $this->isRangeEqual($x - 1 + $bottomL1, $y - 1 + $bottomL2)) {
                    $x--;
                    $y--;
                    $snake[2]++;
                }

                $V[1][$limit + $k] = $x;

                if ($isEven && $k >= -$delta - $d && $k <= $d - $delta && $x <= $V[0][$limit + $k + $delta]) {
                    $snake[0] = $bottomL1 + $x;
                    $snake[1] = $bottomL2 + $y;

                    return intval(2 * $d);
                }

                // Check to see if we can cut down our diagonal range.
                if ($x <= 0) {
                    $startBackward = $k + 1;
                    $valueToAddBackward = 0;
                }
                elseif ($y <= 0 && $endBackward > $k - 1) {
                    $endBackward = $k - 1;
                }
            }
        }

        // Computing the true LCS is too expensive, instead find the diagonal with the most progress and pretend a
        // middle snake of length 0 occurs there.
        /** @var int[] */
        $mostProgress = static::findMostProgress($M, $N, $limit, $V);

        $snake[0] = $bottomL1 + $mostProgress[0];
        $snake[1] = $bottomL2 + $mostProgress[1];
        $snake[2] = 0;

        return 5;

        // HACK: since we didn't really finish the LCS computation we don't really know the length of the SES. We don't
        // do anything with the result anyway, unless it's <=1. We know for a fact SES > 1 so 5 is as good a number as
        // any to return here.
    }

    /**
     * @param  int     $M
     * @param  int     $N
     * @param  int     $limit
     * @param  int[][] $V
     * @return int[]
     */
    private static function findMostProgress(int $M, int $N, int $limit, array $V): array
    {
        $delta = $N - $M;

        if (($M & 1) == ($limit & 1)) {
            $forwardStartDiag = max(-$M, -$limit);
        } else {
            $forwardStartDiag = max(1 - $M, -$limit);
        }

        $forwardEndDiag = min($N, $limit);

        if (($N & 1) == ($limit & 1)) {
            $backwardStartDiag = max(-$N, -$limit);
        } else {
            $backwardStartDiag = max(1 - $N, -$limit);
        }

        $backwardEndDiag = min($M, $limit);
        $maxProgress = array_fill(0,
            intval(ceil(max($forwardEndDiag - $forwardStartDiag, $backwardEndDiag - $backwardStartDiag) / 2.0 + 1)),
            [0, 0, 0]);
        $numProgress = 0;
        // the 1st entry is current, it is initialized with 0s.

        // First search the forward diagonals.
        for ($k = $forwardStartDiag; $k <= $forwardEndDiag; $k += 2) {
            $x = $V[0][$limit + $k];
            $y = $x - $k;

            if ($x > $N || $y > $M) {
                continue;
            }

            $progress = $x + $y;

            if ($progress > $maxProgress[0][2]) {
                $numProgress = 0;
                $maxProgress[0][0] = $x;
                $maxProgress[0][1] = $y;
                $maxProgress[0][2] = $progress;
            }
            elseif ($progress == $maxProgress[0][2]) {
                $numProgress++;
                $maxProgress[$numProgress][0] = $x;
                $maxProgress[$numProgress][1] = $y;
                $maxProgress[$numProgress][2] = $progress;
            }
        }

        // Progress is in the forward direction.
        $maxProgressForward = true;

        // Now search the backward diagonals.
        for ($k = $backwardStartDiag; $k <= $backwardEndDiag; $k += 2) {
            $x = $V[1][$limit + $k];
            $y = $x - $k - $delta;

            if ($x < 0 || $y < 0) {
                continue;
            }

            $progress = $N - $x + $M - $y;

            if ($progress > $maxProgress[0][2]) {
                $numProgress = 0;
                $maxProgressForward = false;
                $maxProgress[0][0] = $x;
                $maxProgress[0][1] = $y;
                $maxProgress[0][2] = $progress;
            }
            elseif ($progress == $maxProgress[0][2] && !$maxProgressForward) {
                $numProgress++;
                $maxProgress[$numProgress][0] = $x;
                $maxProgress[$numProgress][1] = $y;
                $maxProgress[$numProgress][2] = $progress;
            }
        }

        // Return the middle diagonal with maximum progress.
        return $maxProgress[intval(round($numProgress / 2.0))];
    }

    /**
     * @return int
     */
    abstract protected function getLength1(): int;

    /**
     * @return int
     */
    abstract protected function getLength2(): int;

    /**
     * @param  int $i1
     * @param  int $i2
     * @return bool
     */
    abstract protected function isRangeEqual(int $i1, int $i2): bool;

    /**
     * @param  int $sl1
     * @param  int $sl2
     * @return void
     */
    abstract protected function setLcs(int $sl1, int $sl2): void;

    /**
     * @param  int $lcsLength
     * @return void
     */
    abstract protected function initializeLcs(int $lcsLength): void;
}
