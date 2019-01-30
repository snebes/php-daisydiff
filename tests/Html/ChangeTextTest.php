<?php
/**
 * (c) Steve Nebes <snebes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace SN\DaisyDiff\Html;

use PHPUnit\Framework\TestCase;

/**
 * ChangeText Tests.
 */
class ChangeTextTest extends TestCase
{
    public function testElement(): void
    {
        $changeText = new ChangeText();
        $changeText->startElement('span', ['class' => 'test']);
        $changeText->characters('text');
        $changeText->endElement('span');

        $this->assertSame('<span class="test">text</span>', $changeText->getText());
    }
}
