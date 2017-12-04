<?php declare(strict_types=1);

namespace DaisyDiff\Html;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * A comparator that generates a DOM tree of sorts from handling SAX events. Then it can be used to compute the
 * differences between DOM trees and mark elements accordingly.
 */
class TextNodeComparator
{
    /** @var ArrayCollection<TextNode> */
    private $textNodes;

    /** @var ArrayCollection<Modification> */
    private $lastModified;

    /** @var BodyNode */
    private $bodyNode;

    /** @var [] */
    private $locale;

    public function __construct()
    {
        $this->textNodes    = new ArrayCollection();
        $this->lastModified = new ArrayCollection();
    }


}
