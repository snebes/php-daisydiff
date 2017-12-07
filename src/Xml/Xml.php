<?php declare(strict_types=1);

namespace DaisyDiff\Xml;

/**
 * Static XML methods.
 */
class Xml
{
    /**
     * Open XML element.
     *
     * @param  string   $qName
     * @param  iterable $attributes
     * @return string
     */
    public static function openElement(string $qName, iterable $attributes = []): string
    {
        $s = '<' . $qName;

        foreach ($attributes as $qName => $value) {
            $s .= sprintf(' %s="%s"', $qName, $value);
        }

        $s .= '>';

        return $s;
    }

    /**
     * Shortcut to close an XML element.
     *
     * @param  string $qName
     * @return string
     */
    public static function closeElement(string $qName): string
    {
        return sprintf('</%s>', $qName);
    }
}
