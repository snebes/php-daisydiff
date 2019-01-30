<?php
/**
 * (c) Steve Nebes <snebes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace SN\DaisyDiff\Xml;

use SN\DaisyDiff\Html\Dom\DomTreeBuilder;

/**
 * XMLReader implementation.
 */
class XMLReader
{
    /** @var DomTreeBuilder */
    private $domTreeBuilder;

    /**
     * @param DomTreeBuilder $domTreeBuilder
     */
    public function __construct(DomTreeBuilder $domTreeBuilder)
    {
        $this->domTreeBuilder = $domTreeBuilder;
    }

    /**
     * @param string $xml
     * @return void
     *
     * @throws \RuntimeException
     */
    public function parse(string $xml): void
    {
        $this->domTreeBuilder->startDocument();

        // Wrap xml in valid block.
        $clean = \mb_convert_encoding($xml, 'HTML-ENTITIES', 'UTF-8');
        $clean = \sprintf('<?xml version="1.0" encoding="UTF-8"?>%s<body>%s</body>', Sanitizer::getDocType(), $clean);

        // Create parser.
        $parser = \xml_parser_create('');
        \xml_set_element_handler($parser, [$this->domTreeBuilder, 'startElement'], [$this->domTreeBuilder, 'endElement']);
        \xml_set_character_data_handler($parser, [$this->domTreeBuilder, 'characters']);

        // Force xml tags to be lowercase.
        \xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, false);

        // Parse!
        if (!\xml_parse($parser, $clean, true)) {
            $error = \xml_error_string(xml_get_error_code($parser));
            $line  = \xml_get_current_line_number($parser);
            $col   = \xml_get_current_column_number($parser);

            throw new \RuntimeException("XML Error: {$error} at line {$line} and column {$col}\n");
        }

        \xml_parser_free($parser);

        $this->domTreeBuilder->endDocument();
    }
}
