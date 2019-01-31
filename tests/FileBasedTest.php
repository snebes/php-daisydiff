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
     *
     * @dataProvider listTests
     */
    public function testFiles(string $original, string $modified, string $expected): void
    {
        $daisy = new DaisyDiff();
        $actual = $daisy->diff($original, $modified);

        $this->assertXmlStringEqualsXmlString($expected, $actual);
    }

    public function listTests()
    {
        $files = $this->listFiles(__DIR__ . '/TestData/General');

        foreach ($files as $testName => $fileList) {
            if (!empty($fileList['a.html']) && !empty($fileList['b.html']) && !empty($fileList['expected.html'])) {

                $expected = \preg_replace('#<head>.*?</head>#', '', $fileList['expected.html']);
                $expected = \preg_replace('#<span class="diff-html-added".*?>(.*?)</span>#', '<ins class="diff-html-added">\1</ins>', $expected);
                $expected = \preg_replace('#<span class="diff-html-removed".*?>(.*?)</span>#', '<del class="diff-html-removed">\1</del>', $expected);

                yield $testName => [$fileList['a.html'], $fileList['b.html'], $expected];
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
