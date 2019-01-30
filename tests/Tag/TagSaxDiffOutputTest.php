<?php
/**
 * (c) Steve Nebes <snebes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace DaisyDiff\Tag;

use DaisyDiff\Html\ChangeText;
use PHPUnit\Framework\TestCase;

/**
 * TagSaxDiffOutput tests
 */
class TagSaxDiffOutputTest extends TestCase
{
    protected function setUp()
    {
        $this->markTestSkipped();
    }

    public function testAddClearPart(): void
    {
        $changeText = new ChangeText();
        $output = new TagSaxDiffOutput($changeText);
        $output->addClearPart("\n<p id=\"sample\">This is a <span style=\"color:blue\">blue</span> book</p>");

        $expected = <<<HTML
HTML;

        $this->assertSame($expected, $changeText->getText());
    }
}
