<?php declare(strict_types=1);

namespace DaisyDiff\Xml;

/**
 * SAX ContentHandlerInterface
 */
interface ContentHandlerInterface
{
    /**
     * @param  string       $qName
     * @param  AttributeBag $attributeBag
     * @return void
     */
    public function startElement(string $qName, AttributeBag $attributeBag = null): void;

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
