<?php
/**
 * (c) Steve Nebes <snebes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace SN\DaisyDiff\Html;

use SN\DaisyDiff\Xml\ContentHandlerInterface;

/**
 * ChangeText model.
 */
class ChangeText implements ContentHandlerInterface
{
    /** @var string */
    private $text = '';

    /**
     * @param string $qName
     * @param array  $attributes
     */
    public function startElement(string $qName, array $attributes = []): void
    {
        $this->text .= '<' . $qName;

        foreach ($attributes as $attr => $value) {
            $this->text .= \sprintf(' %s="%s"', $attr, $value);
        }

        $this->text .= '>';
    }

    /**
     * @param string $qName
     */
    public function endElement(string $qName): void
    {
        $this->text .= \sprintf('</%s>', $qName);
    }

    /**
     * @param string $chars
     */
    public function characters(string $chars): void
    {
        $this->text .= $chars;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->getText();
    }
}
