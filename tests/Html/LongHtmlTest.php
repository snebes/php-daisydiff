<?php declare(strict_types=1);

namespace DaisyDiff\Html;

use DaisyDiff\Html\Ancestor\ChangeText;
use DaisyDiff\Html\Dom\DomTreeBuilder;
use DaisyDiff\Html\Dom\ImageNode;
use DaisyDiff\Html\Dom\TagNode;
use DaisyDiff\Html\Dom\TextNode;
use DaisyDiff\Html\Modification\Modification;
use DaisyDiff\Html\Modification\ModificationType;
use OutOfBoundsException;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use StdClass;

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
