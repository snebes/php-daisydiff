<?php declare(strict_types=1);

namespace DaisyDiff;

use DaisyDiff\Html\Dom\DomTreeBuilder;
use DaisyDiff\Xml\Sanitizer;
use Exception;
use Psr\Log\LoggerInterface;
use tidy;

/**
 * SAXReader implementation.
 */
class SAXReader
{
    /** @var DomTreeBuilder */
    private $contentHandler;

    /** @var LoggerInterface */
    private $logger;

    /**
     * @param  DomTreeBuilder  $contentHandler
     * @param  LoggerInterface $logger
     */
    public function __construct(DomTreeBuilder $contentHandler, ?LoggerInterface $logger = null)
    {
        $this->contentHandler = $contentHandler;
        $this->logger = $logger;
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
        $clean = sprintf('<?xml version="1.0" encoding="UTF-8"?>%s<body>%s</body>', Sanitizer::getDocType(), $xml);

        if ($this->logger) {
            $this->logger->info("Before cleanup:\n\n" . $xml);
            $this->logger->info("After cleanup:\n\n" . $clean);
        }

        // Create parser.
        $parser = xml_parser_create('');
        xml_set_element_handler($parser, [$this->contentHandler, 'startElement'], [$this->contentHandler, 'endElement']);
        xml_set_character_data_handler($parser, [$this->contentHandler, 'characters']);

        // Force xml tags to be lowercase.
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
