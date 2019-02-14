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
use SN\DaisyDiff\Parser\MastermindsParser;
use SN\DaisyDiff\Xml\XMLReader;

/**
 * Daisy Diff is a library that diffs (compares) HTML.
 */
class DaisyDiff
{
    private $domVisitor;

    private $maxLength = 200000;

    public function __construct()
    {
        $this->domVisitor = null;
        $this->maxLength = 200000;
    }

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

    public function parse(string $html)
    {
        if (\mb_strlen($html) > $this->maxLength) {
            $html = \mb_substr($html, 0, $this->maxLength);
        }

        if (!$this->isValidUtf8($html)) {
            return '';
        }

        $html = \str_replace(\chr(0), '', $html);

        try {
            $parser = new MastermindsParser();
            $parsed = $parser->parse($html);
        } catch (\Exception $e) {
            return '';
        }

        return $this->domVisitor->visit($parsed);
    }

    /**
     * Validates that the string is utf8 encoded.
     *
     * @param string $html
     * @return bool
     */
    private function isValidUtf8(string $html): bool
    {
        return '' === $html || 1 === \preg_match('/^./us', $html);
    }
}
