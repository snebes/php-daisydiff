<?php declare(strict_types=1);

namespace DaisyDiff\Html\Ancestor\TagToString;

use DaisyDiff\Html\Ancestor\ChangeText;

/**
 * Anchor <a> tag to string.
 */
class AnchorToString extends TagToString
{
    /**
     * @param  ChangeText $text
     * @param  array      $attributes
     * @return void
     */
    public function addAttributes(ChangeText $text, array $attributes = []): void
    {
        $href = array_key_exists('href', $attributes)? $attributes['href'] : null;

        if (!empty($href)) {
            $text->addText(sprintf(' %s %s', mb_strtolower($this->getWithDestination()), $href));
            unset($attributes['href']);
        }

        parent::addAttributes($text, $attributes);
    }

    /**
     * @return string
     */
    protected function getWithDestination(): string
    {
        return $this->getString('diff-withdestination');
    }
}
