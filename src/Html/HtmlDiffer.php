<?php declare(strict_types=1);

namespace DaisyDiff\Html;

/**
 * Takes TextNodeComparator instances, computes the difference between them, marks the changes, and outputs a merged
 * tree to a [] instance.
 */
class HtmlDiffer
{
    /** @var [] */
    private $output;

    /**
     * Compares two Node Trees.
     *
     * @param  TextNodeComparator $left
     * @param  TextNodeComparator $right
     * @return void
     *
     * @throws SAXException
     */
    public function diff(TextNodeComparator $left, TextNodeComparator $right): void
    {
        $settings = new LCSSettings();
        $settings->useGreedyMethod(false);
    }
}
