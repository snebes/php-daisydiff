<?php declare(strict_types=1);

namespace DaisyDiff\Html;

/**
 * ContentHandlerInterface
 */
interface ContentHandlerInterface
{
    /**
     * @param  string   $qName
     * @param  iterable $attributes
     * @return void
     */
    public function startElement(string $qName, iterable $attributes): void;

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
