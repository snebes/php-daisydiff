<?php declare(strict_types=1);

namespace DaisyDiff\Html\Modification;

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
