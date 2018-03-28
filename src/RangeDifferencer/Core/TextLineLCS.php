<?php //declare(strict_types=1);
//
//namespace DaisyDiff\RangeDifferencer\Core;
//
//class TextLineLCS extends LCS
//{
//    /** @var TextLine[] */
//    private $lines1 = [];
//
//    /** @var TextLine[] */
//    private $lines2 = [];
//
//    /** @var TextLine[][] */
//    private $lcs = [];
//
//    /**
//     * @param TextLine[] $lines1
//     * @param TextLine[] $lines2
//     */
//    public function __construct(array $lines1, array $lines2)
//    {
//        $this->lines1 = $lines1;
//        $this->lines2 = $lines2;
//    }
//
//    /**
//     * @return TextLine[][]
//     */
//    public function getResult(): array
//    {
//        $length = $this->getLength();
//        $result = array_fill(0, 2, []);
//
//        if (0 == $length) {
//            return $result;
//        }
//
//        // Compact and shift the result.
//        $result[0] = $this->compactAndShiftLCS($this->lcs[0], $length, $this->lines1);
//        $result[1] = $this->compactAndShiftLCS($this->lcs[1], $length, $this->lines2);
//
//        return $result;
//    }
//
//    /**
//     * @return int
//     */
//    protected function getLength1(): int
//    {
//        return count($this->lines1);
//    }
//
//    /**
//     * @return int
//     */
//    protected function getLength2(): int
//    {
//        return count($this->lines2);
//    }
//
//    /**
//     * @param  int $i1
//     * @param  int $i2
//     * @return bool
//     */
//    protected function isRangeEqual(int $i1, int $i2): bool
//    {
//        return $this->lines1[$i1]->sameText($this->lines2[$i2]);
//    }
//
//    /**
//     * @param  int $sl1
//     * @param  int $sl2
//     * @return void
//     */
//    protected function setLcs(int $sl1, int $sl2): void
//    {
//        $this->lcs[0][$sl1] = $this->lines1[$sl1];
//        $this->lcs[1][$sl1] = $this->lines2[$sl2];
//    }
//
//    /**
//     * @param  int $lcsLength
//     * @return void
//     */
//    protected function initializeLcs(int $lcsLength): void
//    {
//        $this->lcs = array_fill(0, 2, array_fill(0, $lcsLength, null));
//    }
//
//    /**
//     * This method takes an lcs result interspersed with nulls, compacts it and shifts the LCS chunks as far towards the
//     * front as possible. This tends to produce good results most of the time.
//     *
//     * TODO: investigate what to do about comments. shifting either up or down hurts them.
//     *
//     * @param  TextLine[] $lcsSide
//     * @param  int        $len
//     * @param  TextLine[] $original
//     *
//     * @return TextLine[]
//     */
//    private function compactAndShiftLCS(array $lcsSide, int $len, array $original): array
//    {
//        $result = array_fill(0, $len, null);
//
//        if (0 == $len) {
//            return $result;
//        }
//
//        $j = 0;
//
//        while (is_null($lcsSide[$j])) {
//            $j++;
//        }
//
//        $result[0] = $lcsSide[$j];
//        $j++;
//
//        for ($i = 1; $i < $len; $i++) {
//            while (is_null($lcsSide[$j])) {
//                $j++;
//            }
//
//            if ($original[$result[$i - 1]->lineNumber() + 1]->sameText($lcsSide[$j])) {
//                $result[$i] = $original[$result[$i - 1]->lineNumber() + 1];
//            } else {
//                $result[$i] = $lcsSide[$j];
//            }
//
//            $j++;
//        }
//
//        return $result;
//    }
//
//    /**
//     * Breaks the given text up into lines and returns an array of TextLine objects each corresponding to a single line,
//     * ordered according to the line number. That is result[i].lineNumber() == i and is the i'th line in text (starting
//     * from 0) Note: there are 1 more lines than there are newline characters in text. Corollary 1: if the last
//     * character is newline, the last line is empty Corollary 2: the empty string is 1 line.
//     *
//     * @param  string $text
//     * @return TextLine[]
//     */
//    public static function getTextLines(string $text): array
//    {
//        $lines   = [];
//        $begin   = 0;
//        $end     = self::getEOL($text, 0);
//        $lineNum = 0;
//
//        while ($end != -1) {
//            $lines[] = new TextLine($lineNum++, substr($text, $begin, $end - $begin));
//            $begin   = $end + 1;
//            $end     = self::getEOL($text, $begin);
//
//            if ($end == $begin && $text[$begin - 1] == "\r" && $text[$begin] == "\n") {
//                // We have \r\n, skip it.
//                $begin = $end + 1;
//                $end   = self::getEOL($text, $begin);
//            }
//        }
//
//        // This is the last line, no more newline characters, so take the rest of the string.
//        $lines[] = new TextLine($lineNum++, substr($text, $begin));
//
//        return $lines;
//    }
//
//    /**
//     * Returns the index of the next end of line marker ('\n' or '\r') after start.
//     *
//     * @param  string $text
//     * @param  int $start
//     * @return int
//     */
//    private static function getEOL(string $text, int $start): int
//    {
//        $max = strlen($text);
//
//        for ($i = $start; $i < $max; $i++) {
//            $c = substr($text, $i, 1);
//
//            if ($c == "\n" || $c == "\r") {
//                return $i;
//            }
//        }
//
//        return -1;
//    }
//}
