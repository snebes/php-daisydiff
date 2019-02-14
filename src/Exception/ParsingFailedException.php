<?php
/**
 * (c) Steve Nebes <snebes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace SN\DaisyDiff\Exception;

use SN\DaisyDiff\Contracts\ParserInterface;

class ParsingFailedException extends \InvalidArgumentException
{
    /** @var ParserInterface */
    private $parser;

    /**
     * Default values.
     *
     * @param ParserInterface $parser
     * @param \Throwable|null $previous
     */
    public function __construct(ParserInterface $parser, \Throwable $previous = null)
    {
        parent::__construct('HTML parsing failed using ' . \get_class($parser), 0, $previous);

        $this->parser = $parser;
    }

    /**
     * @return ParserInterface
     */
    public function getParser(): ParserInterface
    {
        return $this->parser;
    }
}
