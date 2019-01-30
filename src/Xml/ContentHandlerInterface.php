<?php
/**
 * (c) Steve Nebes <snebes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace SN\DaisyDiff\Xml;

/**
 * SAX ContentHandlerInterface
 */
interface ContentHandlerInterface
{
    /**
     * @param string $qName
     * @param array  $attributes
     * @return void
     */
    public function startElement(string $qName, array $attributes = []): void;

    /**
     * @param string $qName
     * @return void
     */
    public function endElement(string $qName): void;

    /**
     * @param string $chars
     * @return void
     */
    public function characters(string $chars): void;
}
