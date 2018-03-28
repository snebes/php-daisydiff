<?php
//
//namespace RangeDifferencer\Core;
//
//use DaisyDiff\RangeDifferencer\Core\TextLineLCS;
//use PHPUnit\Framework\TestCase;
//
//class TextLineLCSTest extends TestCase
//{
//    public function testLineAddition(): void
//    {
//        $s1 = "abc\ndef\nxyz";
//        $s2 = "abc\ndef\n123\nxyz";
//
//        $l1 = TextLineLCS::getTextLines($s1);
//        $l2 = TextLineLCS::getTextLines($s2);
//
//        $lcs = new TextLineLCS($l1, $l2);
//        $lcs->longestCommonSubsequence();
//        $result = $lcs->getResult();
//
//        $this->assertEquals(3, count($result[0]));
//        $this->assertEquals(3, count($result[1]));
//
//        for ($i = 0; $i < count($result[0]); $i++) {
//            $this->assertTrue($result[0][$i]->sameText($result[1][$i]));
//        }
//
//        $this->assertEquals(0, $result[0][0]->lineNumber());
//        $this->assertEquals(0, $result[1][0]->lineNumber());
//        $this->assertEquals(1, $result[0][1]->lineNumber());
//        $this->assertEquals(1, $result[1][1]->lineNumber());
//        $this->assertEquals(2, $result[0][2]->lineNumber());
//        $this->assertEquals(3, $result[1][2]->lineNumber());
//    }
//
//    public function testLineDeletion(): void
//    {
//        $s1 = "abc\ndef\n123\nxyz";
//        $s2 = "abc\ndef\nxyz";
//
//        $l1 = TextLineLCS::getTextLines($s1);
//        $l2 = TextLineLCS::getTextLines($s2);
//
//        $lcs = new TextLineLCS($l1, $l2);
//        $lcs->longestCommonSubsequence();
//        $result = $lcs->getResult();
//
//        $this->assertEquals(count($result[0]), count($result[1]));
//
//        for ($i = 0; $i < count($result[0]); $i++) {
//            $this->assertTrue($result[0][$i]->sameText($result[1][$i]));
//        }
//
//        $this->assertEquals(0, $result[0][0]->lineNumber());
//        $this->assertEquals(1, $result[0][1]->lineNumber());
//        $this->assertEquals(3, $result[0][2]->lineNumber());
//        $this->assertEquals(0, $result[1][0]->lineNumber());
//        $this->assertEquals(1, $result[1][1]->lineNumber());
//        $this->assertEquals(2, $result[1][2]->lineNumber());
//    }
//
//    public function testLineAppendEnd(): void
//    {
//        $s1 = "abc\ndef";
//        $s2 = "abc\ndef\n123";
//
//        $l1 = TextLineLCS::getTextLines($s1);
//        $l2 = TextLineLCS::getTextLines($s2);
//
//        $lcs = new TextLineLCS($l1, $l2);
//        $lcs->longestCommonSubsequence();
//        $result = $lcs->getResult();
//
//        $this->assertEquals(count($result[0]), count($result[1]));
//        $this->assertEquals(2, count($result[0]));
//
//        for ($i = 0; $i < count($result[0]); $i++) {
//            $this->assertTrue($result[0][$i]->sameText($result[1][$i]));
//        }
//
//        $this->assertEquals(0, $result[0][0]->lineNumber());
//        $this->assertEquals(0, $result[1][0]->lineNumber());
//        $this->assertEquals(1, $result[0][1]->lineNumber());
//        $this->assertEquals(1, $result[1][1]->lineNumber());
//    }
//
//    public function testLineDeleteEnd(): void
//    {
//        $s1 = "abc\ndef\n123";
//        $s2 = "abc\ndef";
//
//        $l1 = TextLineLCS::getTextLines($s1);
//        $l2 = TextLineLCS::getTextLines($s2);
//
//        $lcs = new TextLineLCS($l1, $l2);
//        $lcs->longestCommonSubsequence();
//        $result = $lcs->getResult();
//
//        $this->assertEquals(count($result[0]), count($result[1]));
//        $this->assertEquals(2, count($result[0]));
//
//        for ($i = 0; $i < count($result[0]); $i++) {
//            $this->assertTrue($result[0][$i]->sameText($result[1][$i]));
//        }
//
//        $this->assertEquals(0, $result[0][0]->lineNumber());
//        $this->assertEquals(0, $result[1][0]->lineNumber());
//        $this->assertEquals(1, $result[0][1]->lineNumber());
//        $this->assertEquals(1, $result[1][1]->lineNumber());
//    }
//
//    public function testLineAppendStart(): void
//    {
//        $s1 = "abc\ndef";
//        $s2 = "123\nabc\ndef";
//
//        $l1 = TextLineLCS::getTextLines($s1);
//        $l2 = TextLineLCS::getTextLines($s2);
//
//        $lcs = new TextLineLCS($l1, $l2);
//        $lcs->longestCommonSubsequence();
//        $result = $lcs->getResult();
//
//        $this->assertEquals(count($result[0]), count($result[1]));
//        $this->assertEquals(2, count($result[0]));
//
//        for ($i = 0; $i < count($result[0]); $i++) {
//            $this->assertTrue($result[0][$i]->sameText($result[1][$i]));
//        }
//
//        $this->assertEquals(0, $result[0][0]->lineNumber());
//        $this->assertEquals(1, $result[1][0]->lineNumber());
//        $this->assertEquals(1, $result[0][1]->lineNumber());
//        $this->assertEquals(2, $result[1][1]->lineNumber());
//    }
//
//    public function testLineDeleteStart(): void
//    {
//        $s1 = "123\nabc\ndef";
//        $s2 = "abc\ndef";
//
//        $l1 = TextLineLCS::getTextLines($s1);
//        $l2 = TextLineLCS::getTextLines($s2);
//
//        $lcs = new TextLineLCS($l1, $l2);
//        $lcs->longestCommonSubsequence();
//        $result = $lcs->getResult();
//
//        $this->assertEquals(count($result[0]), count($result[1]));
//        $this->assertEquals(2, count($result[0]));
//
//        for ($i = 0; $i < count($result[0]); $i++) {
//            $this->assertTrue($result[0][$i]->sameText($result[1][$i]));
//        }
//
//        $this->assertEquals(1, $result[0][0]->lineNumber());
//        $this->assertEquals(2, $result[0][1]->lineNumber());
//        $this->assertEquals(0, $result[1][0]->lineNumber());
//        $this->assertEquals(1, $result[1][1]->lineNumber());
//    }
//}
