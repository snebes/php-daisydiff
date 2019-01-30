<?php
/**
 * (c) Steve Nebes <snebes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace DaisyDiff\Output;

use Exception;

/**
 * Interface for classes that need to process the result from the tag-like representation of the output.
 */
interface TextDiffOutputInterface
{
    /**
     * @param string $text
     */
    public function addClearPart(string $text): void;

    /**
     * @param string $text
     */
    public function addRemovedPart(string $text): void;

    /**
     * @param string $text
     */
    public function addAddedPart(string $text): void;
}
