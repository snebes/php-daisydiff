<?php
/**
 * (c) Steve Nebes <snebes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace SN\DaisyDiff\Html\Modification;

/**
 * Modification Types enumerator.
 */
final class ModificationType
{
    const CHANGED  = 'changed';
    const REMOVED  = 'removed';
    const ADDED    = 'added';
    const CONFLICT = 'conflict';
    const NONE     = 'none';
}
