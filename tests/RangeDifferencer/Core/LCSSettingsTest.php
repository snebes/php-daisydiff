<?php
/**
 * (c) Steve Nebes <snebes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace DaisyDiff\RangeDifferencer\Core;

use PHPUnit\Framework\TestCase;

/**
 * LCSSettings tests
 */
class LCSSettingsTest extends TestCase
{
    public function testTooLong(): void
    {
        $tooLong  = 10000000.0;
        $settings = new LCSSettings();
        $settings->setTooLong($tooLong);

        $this->assertEquals($tooLong, $settings->getTooLong(), '', 00);
    }

    public function testTooLongZero(): void
    {
        $tooLong  = 0;
        $settings = new LCSSettings();
        $settings->setTooLong($tooLong);

        $this->assertEquals($tooLong, $settings->getTooLong(), '', 0);
    }

    public function testTooLongLong(): void
    {
        $tooLong  = 978462375223.21;
        $settings = new LCSSettings();
        $settings->setTooLong($tooLong);

        $this->assertEquals($tooLong, $settings->getTooLong(), '', 0);
    }

    public function testTooLongNeg(): void
    {
        $tooLong  = -978462375223.21;
        $settings = new LCSSettings();
        $settings->setTooLong($tooLong);

        $this->assertEquals($tooLong, $settings->getTooLong(), '', 0);
    }

    public function testPowLimit(): void
    {
        $powLimit = 35.50;
        $settings = new LCSSettings();
        $settings->setPowLimit($powLimit);

        $this->assertEquals($powLimit, $settings->getPowLimit(), '', 0);
    }

    public function testPowLimitZero(): void
    {
        $powLimit = 0;
        $settings = new LCSSettings();
        $settings->setPowLimit($powLimit);

        $this->assertEquals($powLimit, $settings->getPowLimit(), '', 0);
    }

    public function testPowLimitLong(): void
    {
        $powLimit = 978462375223.21;
        $settings = new LCSSettings();
        $settings->setPowLimit($powLimit);

        $this->assertEquals($powLimit, $settings->getPowLimit(), '', 0);
    }

    public function testPowLimitNeg(): void
    {
        $powLimit = -978462375223.21;
        $settings = new LCSSettings();
        $settings->setPowLimit($powLimit);

        $this->assertEquals($powLimit, $settings->getPowLimit(), '', 0);
    }

    public function testUseGreedyMethod(): void
    {
        $settings = new LCSSettings();
        $settings->setUseGreedyMethod(true);
        $this->assertTrue($settings->isUseGreedyMethod());

        $settings->setUseGreedyMethod(false);
        $this->assertFalse($settings->isUseGreedyMethod());
    }
}
