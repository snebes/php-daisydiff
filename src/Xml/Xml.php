<?php

declare(strict_types=1);

namespace DaisyDiff\Xml;

/**
 * Static XML methods.
 */
class Xml
{
    /**
     * Open XML element.
     *
     * @param string $qName
     * @param array  $attributes
     * @return string
     */
    public static function openElement(string $qName, array $attributes = []): string
    {
        $s = '<' . $qName;

        foreach ($attributes as $attr => $value) {
            $s .= \sprintf(' %s="%s"', $attr, $value);
        }

        $s .= '>';

        return $s;
    }

    /**
     * Shortcut to close an XML element.
     *
     * @param string $qName
     * @return string
     */
    public static function closeElement(string $qName): string
    {
        return \sprintf('</%s>', $qName);
    }
}
