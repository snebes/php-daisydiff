<?php
/**
 * (c) Steve Nebes <snebes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace DaisyDiff\Html\Modification;

/**
 * This class holds the removal or addition of HTML tags around text. It contains the same information that is presented
 * in the tooltips of default Daisy Diff HTML output.
 *
 * This class is not used internally by DaisyDiff. It does not take any part in the diff process. It is simply provided
 * for applications that use the DaisyDiff library and need more information on the results.
 */
final class HtmlLayoutChange
{
    /** @const string */
    const TAG_ADDED   = 'added';
    const TAG_REMOVED = 'removed';

    /** @var string */
    private $type;

    /** @var string */
    private $openingTag = '';

    /** @var string */
    private $endingTag = '';

    /**
     * @return string|null
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @param string|null $value
     */
    public function setType(?string $value): void
    {
        $this->type = $value;
    }

    /**
     * @return string|null
     */
    public function getOpeningTag(): ?string
    {
        return $this->openingTag;
    }

    /**
     * @param string|null $value
     */
    public function setOpeningTag(?string $value): void
    {
        $this->openingTag = $value;
    }

    /**
     * @return string|null
     */
    public function getEndingTag(): ?string
    {
        return $this->endingTag;
    }

    /**
     * @param string|null $value
     */
    public function setEndingTag(?string $value): void
    {
        $this->endingTag = $value;
    }
}
