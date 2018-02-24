<?php

namespace DaisyDiff\RangeDifferencer\Core;

class TextLineLCS extends LCS
{
    /** @var TextLine[] */
    private $lines1 = [];

    /** @var TextLine[] */
    private $lines2 = [];

    /** @var TextLine[][] */
    private $lcs = [];

    /**
     * @param TextLine[] $lines1
     * @param TextLine[] $lines2
     */
    public function __construct(array $lines1, array $lines2)
    {
        $this->lines1 = $lines1;
        $this->lines2 = $lines2;
    }

    /**
     * @return TextLine[][]
     */
    public function getResult(): array
    {
        $length = $this->getLength();
        $result = array_fill(0, 2, []);

        if (0 == $length) {
            return $result;
        }

        // Compact and shift the result.
        $result[0] = $this->compactAndShiftLCS($this->lcs[0], $length, $this->lines1);
        $result[1] = $this->compactAndShiftLCS($this->lcs[1], $length, $this->lines2);

        return $result;
    }

    /**
     * @return int
     */
    protected function getLength1(): int
    {
        return count($this->lines1);
    }

    /**
     * @return int
     */
    protected function getLength2(): int
    {
        return count($this->lines2);
    }

    /**
     * @param  int $i1
     * @param  int $i2
     * @return bool
     */
    protected function isRangeEqual(int $i1, int $i2): bool
    {
        return $this->lines1[$i1]->sameText($this->lines2[$i2]);
    }

    /**
     * @param  int $sl1
     * @param  int $sl2
     * @return void
     */
    protected function setLcs(int $sl1, int $sl2): void
    {
        $this->lcs[0][$sl1] = $this->lines1[$sl1];
        $this->lcs[1][$sl1] = $this->lines2[$sl2];
    }

    /**
     * @param  int $lcsLength
     * @return void
     */
    protected function initializeLcs(int $lcsLength): void
    {
        $this->lcs = array_fill(0, 2, array_fill(0, $lcsLength, 0));
    }

    /**
     * This method takes an lcs result interspersed with nulls, compacts it and shifts the LCS chunks as far towards the
     * front as possible. This tends to produce good results most of the time.
     *
     * TODO: investigate what to do about comments. shifting either up or down hurts them.
     *
     * @param  TextLine[] $lcsSide
     * @param  int        $len
     * @param  TextLine[] $original
     *
     * @return TextLine[]
     */
    private function compactAndShiftLCS(array $lcsSide, int $len, array $original): array
    {
        $result = [];

        if (0 == $len) {
            return $result;
        }

        $j = 0;

        while (is_null($lcsSide[$j])) {
            $j++;
        }

        $result[0] = $lcsSide[$j];
        $j++;

        for ($i = 1; $i < $len; $i++) {
            while (is_null($lcsSide[$j])) {
                $j++;
            }

            if ($original[$result[$i - 1]->lineNumber() + 1]->sameText($lcsSide[$j])) {
                $result[$i] = $original[$result[$i - 1]->lineNumber() + 1];
            } else {
                $result[$i] = $lcsSide[$j];
            }

            $j++;
        }

        return $result;
    }

    public function getTextLines(string $text): array
    {

    }
}
