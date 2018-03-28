<?php declare(strict_types=1);

namespace DaisyDiff\Html;

use DaisyDiff\Html\Ancestor\ChangeText;
use DaisyDiff\Html\Dom\DomTreeBuilder;
use DaisyDiff\SAXReader;
use Exception;

/**
 * TestCase for HTML diffing. Can be used in unit tests. See HtmlDifferText for example.
 */
class HtmlTestFixture
{
    /** Prevent instantiation */
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
        $oldSax     = new SAXReader($oldHandler);
        $oldSax->parse($oldText);

        // Parse $newText.
        $newHandler = new DomTreeBuilder();
        $newSax     = new SAXReader($newHandler);
        $newSax->parse($newText);

        // Diff.
        $leftComparator  = new TextNodeComparator($oldHandler);
        $rightComparator = new TextNodeComparator($newHandler);

        $content = new ChangeText(55);
        $handler = new DelegatingContentHandler($content);
        $output  = new HtmlSaxDiffOutput($handler, 'test');
        $differ  = new HtmlDiffer($output);
        $differ->diff($leftComparator, $rightComparator);

        return strval($content);
    }

    /**
     * @param  string $ancestor
     * @param  string $oldText
     * @param  string $newText
     * @return string
     * @throws
     */
    public static function diff3(string $ancestor, string $oldText, string $newText): string
    {
        // Parse $ancestor.
        $ancestorHandler = new DomTreeBuilder();
        $ancestorSax     = new SAXReader($ancestorHandler);
        $ancestorSax->parse($ancestor);

        // Parse $oldText.
        $oldHandler = new DomTreeBuilder();
        $oldSax     = new SAXReader($oldHandler);
        $oldSax->parse($oldText);

        // Parse $newText.
        $newHandler = new DomTreeBuilder();
        $newSax     = new SAXReader($newHandler);
        $newSax->parse($newText);

        // Diff.
        $ancestorComparator = new TextNodeComparator($ancestorHandler);
        $leftComparator     = new TextNodeComparator($oldHandler);
        $rightComparator    = new TextNodeComparator($newHandler);

        $content = new ChangeText(55);
        $handler = new DelegatingContentHandler($content);
        $output  = new HtmlSaxDiffOutput($handler, 'test');
        $differ  = new HtmlDiffer($output);
        $differ->diff3($ancestorComparator, $leftComparator, $rightComparator);

        return strval($content);
    }
}
