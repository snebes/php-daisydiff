<?php
/**
 * (c) Steve Nebes <snebes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace SN\DaisyDiff;

use SN\DaisyDiff\Html\ChangeText;
use SN\DaisyDiff\Html\Dom\DomTreeBuilder;
use SN\DaisyDiff\Html\HtmlDiffer;
use SN\DaisyDiff\Html\HtmlSaxDiffOutput;
use SN\DaisyDiff\Html\TextNodeComparator;
use SN\DaisyDiff\Xml\XMLReader;

/**
 * Daisy Diff is a library that diffs (compares) HTML.
 */
class DaisyDiff
{
    /**
     * Diffs two HTML strings, returning the result.
     *
     * @param string $oldText
     * @param string $newText
     * @return string
     */
    public function diff(string $oldText, string $newText): string
    {
        // Parse $old XML.
        $oldHandler = new DomTreeBuilder();
        $reader1 = new XMLReader($oldHandler);
        $reader1->parse($oldText);

        // Parse $new XML.
        $newHandler = new DomTreeBuilder();
        $reader2 = new XMLReader($newHandler);
        $reader2->parse($newText);

        // Comparators.
        $leftComparator = new TextNodeComparator($oldHandler);
        $rightComparator = new TextNodeComparator($newHandler);

        $changeText = new ChangeText();
        $output = new HtmlSaxDiffOutput($changeText);
        $differ = new HtmlDiffer($output);
        $differ->diff($leftComparator, $rightComparator);

        return $changeText->getText();
    }
}
