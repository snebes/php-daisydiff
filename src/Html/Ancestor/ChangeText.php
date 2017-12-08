<?php declare(strict_types=1);

namespace DaisyDiff\Html\Ancestor;

/**
 * ChangeText model.
 */
class ChangeText
{
    /** @var int */
    private $maxNumCharsPerLine;

    /** @var string */
    private $text;

    /** @var string */
    private $newLine = '<br/>';

    /** @var int */
    private $charsThisLine = 0;

    /**
     * @param  int $maxNumCharsPerLine
     */
    public function __construct(int $maxNumCharsPerLine)
    {
        $this->maxNumCharsPerLine = $maxNumCharsPerLine;
    }

    /**
     * @param  string $s
     * @return void
     */
    public function addText(string $s): void
    {
        $s = $this->clean($s);

        // fancy add?  nope!

        $this->text .= $s;
    }

    /**
     * @param  string $s
     * @return void
     */
    public function addHtml(string $s): void
    {
        $this->text .= $s;
    }

    /**
     * @return void
     */
    public function addNewLine(): void
    {
        $this->addHtml($this->newLine);
        $this->charsThisLine = 0;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->text;
    }

    /**
     * @param  string $s
     * @return string
     */
    private function clean(string $s): string
    {
        $search  = array("\n", "\r", '<', '>', "'", '"');
        $replace = array('', '', '&lt;', '&gt;', '&#39;', '&#34;');

        return str_replace($search, $replace, $s);
    }
}
