<?php
/**
 * (c) Steve Nebes <snebes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace SN\DaisyDiff\Html\Ancestor\TagToString;

use SN\DaisyDiff\Html\ChangeText;

/**
 * Anchor <a> tag to string.
 */
class AnchorToString extends TagToString
{
    /**
     * @param ChangeText $text
     * @param array      $attributes
     */
    public function addAttributes(ChangeText $text, array $attributes = []): void
    {
        $href = \array_key_exists('href', $attributes) ? $attributes['href'] : null;

        if (!empty($href)) {
            $text->characters(\sprintf(' %s %s', \mb_strtolower($this->getWithDestination()), $href));
            unset($attributes['href']);
        }

        parent::addAttributes($text, $attributes);
    }

    /**
     * @return string
     */
    private function getWithDestination(): string
    {
        return $this->getString('diff-withdestination');
    }
}
