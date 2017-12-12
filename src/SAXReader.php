<?php declare(strict_types=1);

namespace DaisyDiff;

use DaisyDiff\Html\Dom\DomTreeBuilder;
use Exception;
use tidy;

/**
 * SAXReader implementation.
 */
class SAXReader
{
    /** @var DomTreeBuilder */
    private $contentHandler;

    /**
     * @param  DomTreeBuilder $contentHandler
     */
    public function __construct(DomTreeBuilder $contentHandler)
    {
        $this->contentHandler = $contentHandler;
    }

    /**
     * @param  string $xml
     * @return void
     *
     * @throws Exception
     */
    public function parse(string $xml): void
    {
        // Wrap xml in valid block.
        $tidyConfig = [
            'add-xml-decl'      => true,
            'char-encoding'     => 'utf8',
            'input-encoding'    => 'utf8',
            'output-encoding'   => 'utf8',
            'output-xml'        => true,
        ];
        $tidy  = new tidy();
        $clean = $tidy->repairString($xml, $tidyConfig, 'utf8');

        // Create parser.
        $parser = xml_parser_create('UTF-8');
        xml_set_element_handler($parser, [$this->contentHandler, 'startElement'], [$this->contentHandler, 'endElement']);
        xml_set_character_data_handler($parser, [$this->contentHandler, 'characters']);
        xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, false);

        // Parse!
        if (!xml_parse($parser, $clean, true)) {
            $error = xml_error_string(xml_get_error_code($parser));
            $line  = xml_get_current_line_number($parser);
            $col   = xml_get_current_column_number($parser);

            throw new Exception("XML Error: {$error} at line {$line} and column {$col}\n");
        }

        xml_parser_free($parser);
        unset($parser);
    }
}
