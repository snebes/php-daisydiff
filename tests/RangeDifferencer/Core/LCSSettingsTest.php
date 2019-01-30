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
    /**
     * @param float $tooLong
     *
     * @dataProvider tooLongTests
     */
    public function testTooLong(float $tooLong): void
    {
        $settings = new LCSSettings();
        $settings->setTooLong($tooLong);

        $this->assertEquals($tooLong, $settings->getTooLong(), '', 0);
    }

    public function tooLongTests()
    {
        yield 'default' => [10000000.0];
        yield 'zero' => [0];
        yield 'long' => [978462375223.21];
        yield 'negative' => [-978462375223.21];
    }

    /**
     * @param float $powLimit
     *
     * @dataProvider tooLongTests
     */
    public function testPowLimit(float $powLimit): void
    {
        $settings = new LCSSettings();
        $settings->setPowLimit($powLimit);

        $this->assertEquals($powLimit, $settings->getPowLimit(), '', 0);
    }

    public function powLimitTests()
    {
        yield 'default' => [35.50];
        yield 'zero' => [0];
        yield 'long' => [978462375223.21];
        yield 'negative' => [-978462375223.21];
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
