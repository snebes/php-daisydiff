<?php declare(strict_types=1);

namespace DaisyDiff;

use DaisyDiff\Html\Ancestor\ChangeText;
use DaisyDiff\Html\DelegatingContentHandler;
use DaisyDiff\Html\Dom\DomTreeBuilder;
use DaisyDiff\Html\HtmlDiffer;
use DaisyDiff\Html\HtmlSaxDiffOutput;
use DaisyDiff\Html\TextNodeComparator;
use Exception;
use Psr\Log\LoggerInterface;

/**
 * DaisyDiff
 */
class DaisyDiff
{
    /** @var LoggerInterface */
    private $logger;

    public function __construct(?LoggerInterface $logger = null)
    {
        $this->logger = $logger;
    }

    /**
     * @param  string $old
     * @param  string $new
     * @return string
     *
     * @throws Exception
     */
    public function diff(string $old, string $new): string
    {
        // Parse $old XML.
        $oldHandler = new DomTreeBuilder();
        $oldHandler->startDocument();
        $sax1 = new SAXReader($oldHandler, $this->logger);
        $sax1->parse($old);
        $oldHandler->endDocument();

        // Parse $new XML.
        $newHandler = new DomTreeBuilder();
        $newHandler->startDocument();
        $sax2 = new SAXReader($newHandler, $this->logger);
        $sax2->parse($new);
        $newHandler->endDocument();

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
