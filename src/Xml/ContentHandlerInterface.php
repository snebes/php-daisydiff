<?php declare(strict_types=1);

namespace DaisyDiff\Xml;

/**
 * SAX ContentHandlerInterface
 */
interface ContentHandlerInterface
{
    /**
     * @param  string $qName
     * @param  array  $attributes
     * @return void
     */
    public function startElement(string $qName, array $attributes = []): void;

    /**
     * @param  string $qName
     * @return void
     */
    public function endElement(string $qName): void;

    /**
     * @param  string $chars
     * @return void
     */
    public function characters(string $chars): void;
}
