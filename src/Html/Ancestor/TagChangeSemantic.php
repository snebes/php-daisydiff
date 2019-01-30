<?php
/**
 * (c) Steve Nebes <snebes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace SN\DaisyDiff\Html\Ancestor;

/**
 * Semantic types.
 */
final class TagChangeSemantic
{
    const MOVED   = 'moved';
    const STYLE   = 'style';
    const UNKNOWN = 'unknown';
}
