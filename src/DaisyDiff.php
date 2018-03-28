<?php declare(strict_types=1);

namespace DaisyDiff;

use DaisyDiff\Html\Ancestor\ChangeText;
use DaisyDiff\Html\DelegatingContentHandler;
use DaisyDiff\Html\Dom\DomTreeBuilder;
use DaisyDiff\Html\HtmlDiffer;
use DaisyDiff\Html\HtmlSaxDiffOutput;
use DaisyDiff\Html\TextNodeComparator;
use DaisyDiff\Tag\TagComparator;
use DaisyDiff\Tag\TagDiffer;
use DaisyDiff\Tag\TagSaxDiffOutput;
use Exception;
use Psr\Log\LoggerInterface;

/**
 * DaisyDiff
 */
class DaisyDiff
{
    /** @var LoggerInterface */
    private $logger;

    /**
     * @param LoggerInterface|null $logger
     */
    public function __construct(?LoggerInterface $logger = null)
    {
        $this->logger = $logger;
    }

    /**
     * @param  string $old
     * @param  string $new
     * @return string
     * @throws Exception
     */
    public function diff(string $old, string $new): string
    {
        // Parse $old XML.
        $oldHandler = new DomTreeBuilder();
        $sax1 = new SAXReader($oldHandler, $this->logger);
        $sax1->parse($old);

        // Parse $new XML.
        $newHandler = new DomTreeBuilder();
        $sax2 = new SAXReader($newHandler, $this->logger);
        $sax2->parse($new);

        // Comparators.
        $leftComparator  = new TextNodeComparator($oldHandler);
        $rightComparator = new TextNodeComparator($newHandler);

        $content = new ChangeText(50);
        $handler = new DelegatingContentHandler($content);
        $output  = new HtmlSaxDiffOutput($handler, 'diff');
        $differ  = new HtmlDiffer($output);
        $differ->diff($leftComparator, $rightComparator);

        return strval($content);
    }

    /**
     * @param  string $oldText
     * @param  string $newText
     * @return string
     * @throws Exception
     */
    public function diffTag(string $oldText, string $newText): string
    {
        $oldComp = new TagComparator($oldText);
        $newComp = new TagComparator($newText);

        $content = new ChangeText(50);
        $handler = new DelegatingContentHandler($content);
        $output  = new TagSaxDiffOutput($handler);
        $differ  = new TagDiffer($output);
        $differ->diff($oldComp, $newComp);

        return strval($content);
    }
}
