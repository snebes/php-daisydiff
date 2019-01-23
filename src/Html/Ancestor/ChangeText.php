<?php
/**
 * (c) Steve Nebes <snebes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace DaisyDiff\Html\Ancestor;

/**
 * ChangeText model.
 */
class ChangeText
{
    /** @var string */
    private $text = '';

    /**
     * @param string $s
     */
    public function addText(string $s): void
    {
        $s = $this->clean($s);

        // fancy add?  nope!
        $this->text .= $s;
    }

    /**
     * @param string $s
     */
    public function addHtml(string $s): void
    {
        $this->text .= $s;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->text;
    }

    /**
     * @param string $s
     * @return string
     */
    private function clean(string $s): string
    {
        $search = ["\n", "\r", '<', '>', "'", '"'];
        $replace = ['', '', '&lt;', '&gt;', '&#39;', '&#34;'];

        return \str_replace($search, $replace, $s);
    }
}
