<?php declare(strict_types=1);

namespace DaisyDiff\Html\Ancestor\TagToString;

use DaisyDiff\Html\Ancestor\ChangeText;
use DaisyDiff\Html\Dom\TagNode;

class AnchorToString extends TagToString
{
    /**
     * @param  TagNode $node
     * @param  string  $sem
     */
    public function __construct(TagNode $node, string $sem)
    {
        parent::__construct($node, $sem);
    }

    /**
     * @param  ChangeText $text
     * @param  iterable   $attributes
     * @return void
     */
    public function addAttributes(ChangeText $text, iterable $attributes = []): void
    {
        $newAttrs = array_merge($attributes, []);
        $href = array_key_exists('href', $newAttrs)? $newAttrs['href'] : null;

        if (!empty($href)) {
            $text->addText(sprintf(' %s %s', mb_strtolower($this->getWithDestination()), $href));
            unset($newAttrs['href']);
        }

        parent::addAttributes($text, $newAttrs);
    }

    /**
     * @return string
     */
    protected function getWithDestination(): string
    {
        return $this->getString('diff-withdestination');
    }
}
