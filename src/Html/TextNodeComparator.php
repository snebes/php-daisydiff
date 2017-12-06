<?php declare(strict_types=1);

namespace DaisyDiff\Html;

use IteratorAggregate;

/**
 * A comparator that generates a DOM tree of sorts from handling SAX events. Then it can be used to compute the
 * differences between DOM trees and mark elements accordingly.
 */
class TextNodeComparator implements IteratorAggregate
{
    /** @var TextNode[] */
    private $textNodes = [];

    /** @var Modification[] */
    private $lastModified = [];

    /** @var BodyNode */
    private $bodyNode;

    /** @var [] */
    private $locale;

    public function __construct()
    {
    }


}
