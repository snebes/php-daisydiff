<?php
/**
 * (c) Steve Nebes <snebes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace DaisyDiff\Html;

use DaisyDiff\Html\Dom\DomTreeBuilder;
use DaisyDiff\Xml\XMLReader;

/**
 * TestCase for HTML diffing. Can be used in unit tests. See HtmlDifferText for example.
 */
class HtmlTestFixture
{
    /**
     * Prevent instantiation.
     *
     * @codeCoverageIgnore
     */
    private function __construct()
    {
    }

    /**
     * Performs HTML diffing on two HTML strings. Notice that the input strings are "cleaned-up" first (e.g. all html
     * tags are converted to lowercase).
     *
     * @param  string $oldText
     * @param  string $newText
     * @return string
     * @throws
     */
    public static function diff(string $oldText, string $newText): string
    {
        // Parse $oldText.
        $oldHandler = new DomTreeBuilder();
        $oldSax = new XMLReader($oldHandler);
        $oldSax->parse($oldText);

        // Parse $newText.
        $newHandler = new DomTreeBuilder();
        $newSax = new XMLReader($newHandler);
        $newSax->parse($newText);

        // Diff.
        $leftComparator = new TextNodeComparator($oldHandler);
        $rightComparator = new TextNodeComparator($newHandler);

        $changeText = new ChangeText();
        $output = new HtmlSaxDiffOutput($changeText);

        $differ = new HtmlDiffer($output);
        $differ->diff($leftComparator, $rightComparator);

        return $changeText->getText();
    }

    /**
     * @param  string $ancestor
     * @param  string $oldText
     * @param  string $newText
     * @return string
     * @throws
     */
//    public static function diff3(string $ancestor, string $oldText, string $newText): string
//    {
//        // Parse $ancestor.
//        $ancestorHandler = new DomTreeBuilder();
//        $ancestorSax     = new XMLReader($ancestorHandler);
//        $ancestorSax->parse($ancestor);
//
//        // Parse $oldText.
//        $oldHandler = new DomTreeBuilder();
//        $oldSax     = new XMLReader($oldHandler);
//        $oldSax->parse($oldText);
//
//        // Parse $newText.
//        $newHandler = new DomTreeBuilder();
//        $newSax     = new XMLReader($newHandler);
//        $newSax->parse($newText);
//
//        // Diff.
//        $ancestorComparator = new TextNodeComparator($ancestorHandler);
//        $leftComparator     = new TextNodeComparator($oldHandler);
//        $rightComparator    = new TextNodeComparator($newHandler);
//
//        $content = new ChangeText();
//        $handler = new DelegatingContentHandler($content);
//        $output  = new HtmlSaxDiffOutput($handler, 'test');
//        $differ  = new HtmlDiffer($output);
//        $differ->diff3($ancestorComparator, $leftComparator, $rightComparator);
//
//        return $content->__toString();
//    }
}
