<?php declare(strict_types=1);

namespace DaisyDiff;

use Exception;
use PHPUnit\Framework\TestCase;

class DaisyDiffTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testOutOfBoundsExample1(): void
    {
        $html1 = '<html><body>var v2</body></html>';
        $html2 = "<html>  \n  <body>  \n  Hello world  \n  </body>  \n  </html>";

        $daisy = new DaisyDiff();
        $daisy->diff($html1, $html2);

        $this->assertTrue(true);
    }

    /**
     * @throws Exception
     */
//    public function testOutOfBoundsExample2(): void
//    {
//        $html1 = '<html><body>var v2</body></html>';
//        $html2 = "<html>  \n  <body>  \n  Hello world  \n  </body>  \n  </html>";
//
//        $daisy = new DaisyDiff();
//        $daisy->diffTag($html1, $html2);
//
//        $this->assertTrue(true);
//    }

    /**
     * @throws Exception
     */
//    public function testOutOfBoundsExample3(): void
//    {
//        $html1 = '<html><body>var v2</body></html>';
//        $html2 = "<html>  \n  <body>  \n  Hello world  \n  </body>  \n  </html>";
//
//        $daisy = new DaisyDiff();
//        $daisy->diffTag($html1, $html2);
//
//        $this->assertTrue(true);
//    }

    /**
     * @throws Exception
     */
    public function testOutOfBoundsExample4(): void
    {
        $html1 = "<html>  \n  <body>  \n  Hello world  \n  </body>  \n  </html>";
        $html2 = '<html><body>var v2</body></html>';

        $daisy = new DaisyDiff();
        $daisy->diff($html1, $html2);

        $this->assertTrue(true);
    }

    /**
     * @throws Exception
     */
//    public function testOutOfBoundsExample5(): void
//    {
//        $html1 = "<html>  \n  <body>  \n  Hello world  \n  </body>  \n  </html>";
//        $html2 = '<html><body>var v2</body></html>';
//
//        $daisy = new DaisyDiff();
//        $daisy->diffTag($html1, $html2);
//
//        $this->assertTrue(true);
//    }

    /**
     * @throws Exception
     */
//    public function testOutOfBoundsExample6(): void
//    {
//        $html1 = "<html>  \n  <body>  \n  Hello world  \n  </body>  \n  </html>";
//        $html2 = '<html><body>var v2</body></html>';
//
//        $daisy = new DaisyDiff();
//        $daisy->diffTag($html1, $html2);
//
//        $this->assertTrue(true);
//    }

    /**
     * @throws Exception
     */
    public function testOutOfBoundsExample7(): void
    {
        $html1 = '<html><head></head><body><p>test</p></body></html>';
        $html2 = '<html><head></head><body></body></html>';

        $daisy = new DaisyDiff();
        $daisy->diff($html1, $html2);

        $this->assertTrue(true);
    }

    /**
     * @throws Exception
     */
//    public function testOutOfBoundsExample8(): void
//    {
//        $html1 = '<html><head></head><body><p>test</p></body></html>';
//        $html2 = '<html><head></head><body></body></html>';
//
//        $daisy = new DaisyDiff();
//        $daisy->diffTag($html1, $html2);
//
//        $this->assertTrue(true);
//    }

    /**
     * @throws Exception
     */
//    public function testOutOfBoundsExample9(): void
//    {
//        $html1 = '<html><head></head><body><p>test</p></body></html>';
//        $html2 = '<html><head></head><body></body></html>';
//
//        $daisy = new DaisyDiff();
//        $daisy->diffTag($html1, $html2);
//
//        $this->assertTrue(true);
//    }

    /**
     * @throws Exception
     */
    public function testOutOfBoundsExample10(): void
    {
        $html1 = '<html><head></head><body></body></html>';
        $html2 = '<html><head></head><body><p>test</p></body></html>';

        $daisy = new DaisyDiff();
        $daisy->diff($html1, $html2);

        $this->assertTrue(true);
    }

    /**
     * @throws Exception
     */
//    public function testOutOfBoundsExample11(): void
//    {
//        $html1 = '<html><head></head><body></body></html>';
//        $html2 = '<html><head></head><body><p>test</p></body></html>';
//
//        $daisy = new DaisyDiff();
//        $daisy->diffTag($html1, $html2);
//
//        $this->assertTrue(true);
//    }

    /**
     * @throws Exception
     */
//    public function testOutOfBoundsExample12(): void
//    {
//        $html1 = '<html><head></head><body></body></html>';
//        $html2 = '<html><head></head><body><p>test</p></body></html>';
//
//        $daisy = new DaisyDiff();
//        $daisy->diffTag($html1, $html2);
//
//        $this->assertTrue(true);
//    }

    /**
     * @throws Exception
     */
    public function testOutOfBoundsExample13(): void
    {
        $html1 = '<html><head></head><body><p>test</p><p>test</p></body></html>';
        $html2 = '<html><head></head><body></body></html>';

        $daisy = new DaisyDiff();
        $daisy->diff($html1, $html2);

        $this->assertTrue(true);
    }

    /**
     * @throws Exception
     */
//    public function testOutOfBoundsExample14(): void
//    {
//        $html1 = '<html><head></head><body><p>test</p><p>test</p></body></html>';
//        $html2 = '<html><head></head><body></body></html>';
//
//        $daisy = new DaisyDiff();
//        $daisy->diffTag($html1, $html2);
//
//        $this->assertTrue(true);
//    }

    /**
     * @throws Exception
     */
//    public function testOutOfBoundsExample15(): void
//    {
//        $html1 = '<html><head></head><body><p>test</p><p>test</p></body></html>';
//        $html2 = '<html><head></head><body></body></html>';
//
//        $daisy = new DaisyDiff();
//        $daisy->diffTag($html1, $html2);
//
//        $this->assertTrue(true);
//    }

    /**
     * @throws Exception
     */
    public function testOutOfBoundsExample16(): void
    {
        $html1 = '<html><head></head><body></body></html>';
        $html2 = '<html><head></head><body><p>test</p><p>test</p></body></html>';

        $daisy = new DaisyDiff();
        $daisy->diff($html1, $html2);

        $this->assertTrue(true);
    }

    /**
     * @throws Exception
     */
//    public function testOutOfBoundsExample17(): void
//    {
//        $html1 = '<html><head></head><body></body></html>';
//        $html2 = '<html><head></head><body><p>test</p><p>test</p></body></html>';
//
//        $daisy = new DaisyDiff();
//        $daisy->diffTag($html1, $html2);
//
//        $this->assertTrue(true);
//    }

    /**
     * @throws Exception
     */
//    public function testOutOfBoundsExample18(): void
//    {
//        $html1 = '<html><head></head><body></body></html>';
//        $html2 = '<html><head></head><body><p>test</p><p>test</p></body></html>';
//
//        $daisy = new DaisyDiff();
//        $daisy->diffTag($html1, $html2);
//
//        $this->assertTrue(true);
//    }
}
