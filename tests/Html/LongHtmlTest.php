<?php declare(strict_types=1);

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

        $this->assertContains('diff-html-changed', $result);
    }
}
