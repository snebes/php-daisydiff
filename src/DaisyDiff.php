<?php
/**
 * (c) Steve Nebes <snebes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace DaisyDiff;

use DaisyDiff\Html\ChangeText;
use DaisyDiff\Html\Dom\DomTreeBuilder;
use DaisyDiff\Html\HtmlDiffer;
use DaisyDiff\Html\HtmlSaxDiffOutput;
use DaisyDiff\Html\TextNodeComparator;
use DaisyDiff\Xml\XMLReader;

/**
 * Daisy Diff is a library that diffs (compares) HTML.
 */
class DaisyDiff
{
    /**
     * Diffs two HTML strings, returning the result.
     *
     * @param string $oldSource
     * @param string $newSource
     * @return string
     */
    public function diff(string $oldSource, string $newSource): string
    {
        // Parse $old XML.
        $oldHandler = new DomTreeBuilder();
        $reader1 = new XMLReader($oldHandler);
        $reader1->parse($oldSource);

        // Parse $new XML.
        $newHandler = new DomTreeBuilder();
        $reader2 = new XMLReader($newHandler);
        $reader2->parse($newSource);

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
