<?php declare(strict_types=1);

namespace DaisyDiff;

use DaisyDiff\Html\Ancestor\ChangeText;
use DaisyDiff\Html\DelegatingContentHandler;
use DaisyDiff\Html\Dom\DomTreeBuilder;
use DaisyDiff\Html\HtmlDiffer;
use DaisyDiff\Html\HtmlSaxDiffOutput;
use DaisyDiff\Html\TextNodeComparator;
use Exception;

/**
 * DaisyDiff
 */
class DaisyDiff
{
    /**
     * @param  string $old
     * @param  string $new
     * @return string
     *
     * @throws Exception
     */
    public function diffHtml(string $old, string $new): string
    {
        // Parse $old XML.
        $oldHandler = new DomTreeBuilder();
        $oldHandler->startDocument();
        $oldXml = sprintf('<?xml version="1.0" encoding="UTF-8"?><body>%s</body>', $old);

        $xmlParser = xml_parser_create('UTF-8');
        xml_set_element_handler($xmlParser, [$oldHandler, 'startElement'], [$oldHandler, 'endElement']);
        xml_set_character_data_handler($xmlParser, [$oldHandler, 'characters']);

        if (!xml_parse($xmlParser, $oldXml, true)) {
            $error = xml_error_string(xml_get_error_code($xmlParser));
            $line  = xml_get_current_line_number($xmlParser);

            throw new Exception("XML Error: {$error} at line {$line}\n");
        }

        xml_parser_free($xmlParser);

        // Parse $new XML.
        $newHandler = new DomTreeBuilder();
        $newHandler->startDocument();
        $newXml = sprintf('<?xml version="1.0" encoding="UTF-8"?><body>%s</body>', $new);

        $xmlParser = xml_parser_create('UTF-8');
        xml_set_element_handler($xmlParser, [$newHandler, 'startElement'], [$newHandler, 'endElement']);
        xml_set_character_data_handler($xmlParser, [$newHandler, 'characters']);

        if (!xml_parse($xmlParser, $newXml, true)) {
            $error = xml_error_string(xml_get_error_code($xmlParser));
            $line  = xml_get_current_line_number($xmlParser);

            throw new Exception("XML Error: {$error} at line {$line}\n");
        }

        xml_parser_free($xmlParser);

        // Diff.
        $leftComparator  = new TextNodeComparator($oldHandler);
        $rightComparator = new TextNodeComparator($newHandler);

        $content = new ChangeText();
        $handler = new DelegatingContentHandler($content);
        $output  = new HtmlSaxDiffOutput($handler, 'test');
        $differ  = new HtmlDiffer($output);
        $differ->diff($leftComparator, $rightComparator);

        return strval($content);
    }
}
