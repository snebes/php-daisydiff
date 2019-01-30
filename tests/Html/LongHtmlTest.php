<?php
/**
 * (c) Steve Nebes <snebes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace DaisyDiff\Html;

use PHPUnit\Framework\TestCase;

/**
 * Unit tests for handling long lines of HTML.
 */
class LongHtmlTest extends TestCase
{
    public function testlongHtml1(): void
    {
        $oldText = '<html> <body> <A HREF="../../javax/realtime/AsyncEventHandler.html#AsyncEventHandler(javax.realtime.SchedulingParameter, b)">AsyncEventHandler</A> </body> </html>';
        $newText = '<html> <body> <A HREF="../../javax/realtime/BsyncEventHandler.html#AsyncEventHandler(javax.realtime.SchedulingParameter, b)">AsyncEventHandler</A> </body> </html>';
        $result = HtmlTestFixture::diff($oldText, $newText);

        $expected = <<<HTML
<html><span class="diff-html-changed"> </span><a HREF="../../javax/realtime/BsyncEventHandler.html#AsyncEventHandler(javax.realtime.SchedulingParameter, b)"><span class="diff-html-changed">AsyncEventHandler</span></a><span class="diff-html-changed"> </span></html>
HTML;

        $this->assertSame($expected, $result);
    }
}
