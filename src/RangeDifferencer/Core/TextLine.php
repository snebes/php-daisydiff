<?php

declare(strict_types=1);
//
//namespace DaisyDiff\RangeDifferencer\Core;
//
//class TextLine
//{
//    /** @var int */
//    private $number = 0;
//
//    /** @var string */
//    private $text = '';
//
//    /**
//     * @param int    $number
//     * @param string $text
//     */
//    public function __construct(int $number, string $text)
//    {
//        $this->number = $number;
//        $this->text   = $text;
//    }
//
//    /**
//     * Compares this TextLine to l and returns true if they have the same text.
//     *
//     * @param TextLine $l
//     * @return bool
//     */
//    public function sameText(TextLine $l): bool
//    {
//        return 0 == strcmp($this->text(), $l->text());
//    }
//
//    /**
//     * Returns the line number of this line.
//     *
//     * @return int
//     */
//    public function lineNumber(): int
//    {
//        return $this->number;
//    }
//
//    public function text(): string
//    {
//        return $this->text;
//    }
//
//    /**
//     * @return string
//     */
//    public function __toString(): string
//    {
//        return sprintf("%d %s\n", $this->number, $this->text);
//    }
//}
