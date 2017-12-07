<?php declare(strict_types=1);

namespace DaisyDiff;

use PHPUnit\Framework\TestCase;

/**
 * DaisyDiff Tests.
 */
class DaisyDiffTest extends TestCase
{
    public function testOutOfBoundsExample1(): void
    {
        $html1 = "<html><body>var v2</body></html>";
        $html2 = "<html>  \n  <body>  \n  Hello world  \n  </body>  \n  </html>";

        $diff = new DaisyDiff();
        $out  = $diff->diffHtml($html1, $html2);

//        var_dump($out);
    }
}
