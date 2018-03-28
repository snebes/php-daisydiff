<?php //declare(strict_types=1);
//
//namespace DaisyDiff\RangeDifferencer;
//
//use PHPUnit\Framework\TestCase;
//use ReflectionException;
//use ReflectionMethod;
//
///**
// * LCS Tests
// */
//class LCSTest extends TestCase
//{
//    /**
//     * @param  int $length1
//     * @param  int $length2
//     * @param  int $b1
//     * @param  int $t1
//     * @param  int $b2
//     * @param  int $t2
//     * @return int
//     * @throws ReflectionException
//     */
//    public function lcsRecHelper(int $length1, int $length2, int $b1, int $t1, int $b2, int $t2): int
//    {
//        $lcs    = new LCSFixture($length1, $length2);
//        $snake  = [0, 0, 0];
//        $V      = array_fill(0, 2, array_fill(0, $length1 + $length2 + 1, 0));
//        $params = [$b1, $t1, $b2, $t2, &$V, &$snake];
//
//        $method = new ReflectionMethod($lcs, 'lcsRec');
//        $method->setAccessible(true);
//
//        return $method->invokeArgs($lcs, $params);
//    }
//
//    public function testLcsRec1(): void
//    {
//        $bottom1 = 0;
//        $top1    = 1;
//        $bottom2 = 0;
//        $top2    = 1;
//        $length1 = 3;
//        $length2 = 3;
//
//        $this->assertEquals(0, $this->lcsRecHelper($length1, $length2, $bottom1, $top1, $bottom2, $top2));
//    }
//
//    public function testLcsRec2(): void
//    {
//        $bottom1 = 0;
//        $top1    = 46;
//        $bottom2 = 0;
//        $top2    = 44;
//        $length1 = 49;
//        $length2 = 47;
//
//        $this->assertEquals(7, $this->lcsRecHelper($length1, $length2, $bottom1, $top1, $bottom2, $top2));
//    }
//}
