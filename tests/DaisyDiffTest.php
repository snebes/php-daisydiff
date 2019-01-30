<?php
/**
 * (c) Steve Nebes <snebes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace DaisyDiff;

use Exception;
use PHPUnit\Framework\TestCase;

class DaisyDiffTest extends TestCase
{
    public function testOutOfBoundsExample1to3(): void
    {
        $html1 = '<html><body>var v2</body></html>';
        $html2 = "<html>  \n  <body>  \n  Hello world  \n  </body>  \n  </html>";

        $daisy = new DaisyDiff();
        $actual = $daisy->diff($html1, $html2);

        $expected = <<<HTML
<html><del class="diff-html-removed">var v2</del></html><html><ins class="diff-html-added"> </ins><ins class="diff-html-added">Hello world </ins></html>
HTML;

        $this->assertSame($expected, $actual);
    }

    public function testOutOfBoundsExample4to6(): void
    {
        $html1 = "<html>  \n  <body>  \n  Hello world  \n  </body>  \n  </html>";
        $html2 = '<html><body>var v2</body></html>';

        $daisy = new DaisyDiff();
        $actual = $daisy->diff($html1, $html2);

        $expected = <<<HTML
<html><del class="diff-html-removed"> </del><del class="diff-html-removed">Hello world </del></html><html><ins class="diff-html-added">var v2</ins></html>
HTML;

        $this->assertSame($expected, $actual);
    }

    public function testOutOfBoundsExample7to9(): void
    {
        $html1 = '<html><head></head><body><p>test</p></body></html>';
        $html2 = '<html><head></head><body></body></html>';

        $daisy = new DaisyDiff();
        $actual = $daisy->diff($html1, $html2);

        $expected = <<<HTML
<p><del class="diff-html-removed">test</del></p><html><head></head></html>
HTML;

        $this->assertSame($expected, $actual);
    }

    public function testOutOfBoundsExample10to12(): void
    {
        $html1 = '<html><head></head><body></body></html>';
        $html2 = '<html><head></head><body><p>test</p></body></html>';

        $daisy = new DaisyDiff();
        $actual = $daisy->diff($html1, $html2);

        $expected = <<<HTML
<html><head></head><p><ins class="diff-html-added">test</ins></p></html>
HTML;

        $this->assertSame($expected, $actual);
    }

    public function testOutOfBoundsExample13to15(): void
    {
        $html1 = '<html><head></head><body><p>test</p><p>test</p></body></html>';
        $html2 = '<html><head></head><body></body></html>';

        $daisy = new DaisyDiff();
        $actual = $daisy->diff($html1, $html2);

        $expected = <<<HTML
<p><del class="diff-html-removed">test</del></p><p><del class="diff-html-removed">test</del></p><html><head></head></html>
HTML;

        $this->assertSame($expected, $actual);
    }

    public function testOutOfBoundsExample16to18(): void
    {
        $html1 = '<html><head></head><body></body></html>';
        $html2 = '<html><head></head><body><p>test</p><p>test</p></body></html>';

        $daisy = new DaisyDiff();
        $actual = $daisy->diff($html1, $html2);

        $expected = <<<HTML
<html><head></head><p><ins class="diff-html-added">test</ins></p><p><ins class="diff-html-added">test</ins></p></html>
HTML;

        $this->assertSame($expected, $actual);
    }
}
