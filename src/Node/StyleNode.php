<?php
/**
 * (c) Steve Nebes <snebes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace SN\DaisyDiff\Node;

/**
 * Style node - ignores content inside script tags.
 */
class StyleNode
{
    use IsChildlessTrait;

    /**
     * @return string
     */
    public function getTagName(): string
    {
        return 'style';
    }

    /**
     * @return string
     */
    public function render(): string
    {
        return '';
    }
}
