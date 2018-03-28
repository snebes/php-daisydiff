<?php declare(strict_types=1);

namespace DaisyDiff\Html\Modification;

/**
 * This class holds the removal or addition of HTML tags around text. It contains the same information that is presented
 * in the tooltips of default Daisy Diff HTML output.
 *
 * This class is not used internally by DaisyDiff. It does not take any part in the diff process. It is simply provided
 * for applications that use the DaisyDiff library and need more information on the results.
 */
final class HtmlLayoutChangeType
{
    /** @const string */
    const TAG_ADDED   = 'added';
    const TAG_REMOVED = 'removed';
}
