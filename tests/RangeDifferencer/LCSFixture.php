<?php //declare(strict_types=1);
//
//namespace DaisyDiff\RangeDifferencer;
//
///**
// * Concrete LCS implementation for testing.
// */
//class LCSFixture extends LCS
//{
//    /** @var int */
//    private $length1 = 0;
//
//    /** @var int */
//    private $length2 = 0;
//
//    /**
//     * @param  int $length1
//     * @param  int $length2
//     */
//    public function __construct(int $length1 = 0, int $length2 = 0)
//    {
//        $this->length1 = $length1;
//        $this->length2 = $length2;
//    }
//
//    /** {@inheritdoc} */
//    public function getLength1(): int
//    {
//        return $this->length1;
//    }
//
//    /** {@inheritdoc} */
//    public function getLength2(): int
//    {
//        return $this->length2;
//    }
//
//    /** {@inheritdoc} */
//    protected function isRangeEqual(int $i1, int $i2): bool
//    {
//        return false;
//    }
//
//    /** {@inheritdoc} */
//    protected function setLcs(int $sl1, int $sl2): void
//    {
//    }
//
//    /** {@inheritdoc} */
//    protected function initializeLcs(int $lcsLength): void
//    {
//    }
//}
