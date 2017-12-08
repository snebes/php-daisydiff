<?php declare(strict_types=1);

namespace DaisyDiff;

use DaisyDiff\Html\Ancestor\ChangeText;
use DaisyDiff\Html\DelegatingContentHandler;
use DaisyDiff\Html\Dom\DomTreeBuilder;
use DaisyDiff\Html\HtmlDiffer;
use DaisyDiff\Html\HtmlSaxDiffOutput;
use DaisyDiff\Html\TextNodeComparator;
use Exception;

/**
 * DaisyDiff
 */
class DaisyDiff
{
    /**
     * @param  string $old
     * @param  string $new
     * @return string
     *
     * @throws Exception
     */
    public function diffHtml(string $old, string $new): string
    {
        // Parse $old XML.
        $oldHandler = new DomTreeBuilder();
        $oldHandler->startDocument();
        $sax1 = new SAXReader($oldHandler);
        $sax1->parse($old);

        // Parse $new XML.
        $newHandler = new DomTreeBuilder();
        $newHandler->startDocument();
        $sax2 = new SAXReader($newHandler);
        $sax2->parse($new);

        // Diff.
        $leftComparator  = new TextNodeComparator($oldHandler);
        $rightComparator = new TextNodeComparator($newHandler);

        $content = new ChangeText(50);
        $handler = new DelegatingContentHandler($content);
        $output  = new HtmlSaxDiffOutput($handler, 'diff');
        $differ  = new HtmlDiffer($output);
        $differ->diff($leftComparator, $rightComparator);

        return strval($content);
    }
}
