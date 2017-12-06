<?php declare(strict_types=1);

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
    /** @var HtmlLayoutChangeType */
    private $type;

    /** @var string */
    private $openingTag = '';

    /** @var string */
    private $endingTag = '';

    /**
     * @return string
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @param  string $type
     * @return self
     */
    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return string
     */
    public function getOpeningTag(): ?string
    {
        return $this->openingTag;
    }

    /**
     * @param  string $value
     * @return self
     */
    public function setOpeningTag(?string $value): self
    {
        $this->openingTag = $value;

        return $this;
    }

    /**
     * @return string
     */
    public function getEndingTag(): ?string
    {
        return $this->endingTag;
    }

    /**
     * @param  string $value
     * @return self
     */
    public function setEndingTag(?string $value): self
    {
        $this->endingTag = $value;

        return $this;
    }
}
