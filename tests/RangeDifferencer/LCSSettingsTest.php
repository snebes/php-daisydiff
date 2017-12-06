<?php declare(strict_types=1);

namespace DaisyDiff\RangeDifferencer;

use PHPUnit\Framework\TestCase;

/**
 * LCSSettings Tests.
 */
class LCSSettingsTest extends TestCase
{
    public function testTooLong(): void
    {
        $tooLong = 10000000.0;
        $lcs = new LCSSettings();
        $lcs->setTooLong($tooLong);

        $this->assertEquals($tooLong, $lcs->getTooLong(), null, 0);
    }

    public function testTooLongZero(): void
    {
        $tooLong = 0;
        $lcs = new LCSSettings();
        $lcs->setTooLong($tooLong);

        $this->assertEquals($tooLong, $lcs->getTooLong(), null, 0);
    }

    public function testTooLongLong(): void
    {
        $tooLong = 978462375223.21;
        $lcs = new LCSSettings();
        $lcs->setTooLong($tooLong);

        $this->assertEquals($tooLong, $lcs->getTooLong(), null, 0);
    }

    public function testTooLongNeg(): void
    {
        $tooLong = -978462375223.21;
        $lcs = new LCSSettings();
        $lcs->setTooLong($tooLong);

        $this->assertEquals($tooLong, $lcs->getTooLong(), null, 0);
    }

    public function testPowLimit(): void
    {
        $powLimit = 35.50;
        $lcs = new LCSSettings();
        $lcs->setPowLimit($powLimit);

        $this->assertEquals($powLimit, $lcs->getPowLimit(), null, 0);
    }

    public function testPowLimitZero(): void
    {
        $powLimit = 0;
        $lcs = new LCSSettings();
        $lcs->setPowLimit($powLimit);

        $this->assertEquals($powLimit, $lcs->getPowLimit(), null, 0);
    }

    public function testPowLimitLong(): void
    {
        $powLimit = 978462375223.21;
        $lcs = new LCSSettings();
        $lcs->setPowLimit($powLimit);

        $this->assertEquals($powLimit, $lcs->getPowLimit(), null, 0);
    }

    public function testPowNeg(): void
    {
        $powLimit = -978462375223.21;
        $lcs = new LCSSettings();
        $lcs->setPowLimit($powLimit);

        $this->assertEquals($powLimit, $lcs->getPowLimit(), null, 0);
    }

    public function testUseGreedyMethod(): void
    {
        $lcs = new LCSSettings();

        $lcs->setGreedyMethod(true);
        $this->assertTrue($lcs->useGreedyMethod());

        $lcs->setGreedyMethod(false);
        $this->assertFalse($lcs->useGreedyMethod());
    }
}
