<?php
/**
 * (c) Steve Nebes <snebes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SN\DaisyDiff;

use PHPUnit\Framework\TestCase;

/**
 * Performs tests from files in TestData.
 */
class FileBasedTest extends TestCase
{
    /**
     * @param string $original
     * @param string $modified
     * @param string $expected
     * @param bool   $skipTest
     *
     * @dataProvider listTests
     * @group        file
     */
    public function testFiles(string $original, string $modified, string $expected, bool $skipTest): void
    {
        if ($skipTest) {
            $this->markTestSkipped();
        }

        $daisy = new DaisyDiff();
        $actual = $daisy->diff($original, $modified);

        $this->assertXmlStringEqualsXmlString($expected, $actual);
    }

    public function listTests()
    {
        $files = $this->listFiles(__DIR__ . '/TestData/General');

        foreach ($files as $testName => $fileList) {
            if (!empty($fileList['a.html']) && !empty($fileList['b.html']) && !empty($fileList['expected.html'])) {
                $original = $fileList['a.html'];
                $modified = $fileList['b.html'];
                $expected = $fileList['expected.html'];
                $skipTest = isset($fileList['skipped']) ? true : false;

                yield $testName => [$original, $modified, $expected, $skipTest];
            }
        }
    }

    private function listFiles(string $directory): array
    {
        $files = [];

        foreach (new \DirectoryIterator($directory) as $fileInfo) {
            if ($fileInfo->isFile()) {
                $fileName = $fileInfo->getFilename();
                $testName = \basename($fileInfo->getPath());
                $file = $fileInfo->openFile();

                $files[$testName][$fileName] = $file->fread($file->getSize());
            } elseif (!$fileInfo->isDot()) {
                $list = $this->listFiles($fileInfo->getPathname());
                $files = \array_merge($files, $list);
            }
        }

        return $files;
    }
}
