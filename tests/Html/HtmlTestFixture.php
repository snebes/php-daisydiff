<?php declare(strict_types=1);

namespace DaisyDiff\Html;

use DaisyDiff\Html\Ancestor\ChangeText;
use DaisyDiff\Html\Dom\DomTreeBuilder;
use Exception;

/**
 * TestCase for HTML diffing. Can be used in unit tests. See HtmlDifferText for example.
 */
class HtmlTestFixture
{
    /** Prevent instantiation */
    private function __construct()
    {
    }

    /**
     * Performs HTML diffing on two HTML strings. Notice that the input strings are "cleaned-up" first (e.g. all html
     * tags are converted to lowercase).
     *
     * @param  string $oldText
     * @param  string $newText
     * @return string
     * @throws
     */
    public static function diff(string $oldText, string $newText): string
    {
        // Parse $old XML.
        $oldHandler = new DomTreeBuilder();
        $oldHandler->startDocument();
        $oldXml = sprintf('<?xml version="1.0" encoding="UTF-8"?><body>%s</body>', $oldText);

        $xmlParser = xml_parser_create('UTF-8');
        xml_set_element_handler($xmlParser, [$oldHandler, 'startElement'], [$oldHandler, 'endElement']);
        xml_set_character_data_handler($xmlParser, [$oldHandler, 'characters']);
        xml_parser_set_option($xmlParser, XML_OPTION_CASE_FOLDING, false);

        if (!xml_parse($xmlParser, $oldXml, true)) {
            $error = xml_error_string(xml_get_error_code($xmlParser));
            $line  = xml_get_current_line_number($xmlParser);

            throw new Exception("XML Error: {$error} at line {$line}\n");
        }

        xml_parser_free($xmlParser);

        // Parse $new XML.
        $newHandler = new DomTreeBuilder();
        $newHandler->startDocument();
        $newXml = sprintf('<?xml version="1.0" encoding="UTF-8"?><body>%s</body>', $newText);

        $xmlParser = xml_parser_create('UTF-8');
        xml_set_element_handler($xmlParser, [$newHandler, 'startElement'], [$newHandler, 'endElement']);
        xml_set_character_data_handler($xmlParser, [$newHandler, 'characters']);
        xml_parser_set_option($xmlParser, XML_OPTION_CASE_FOLDING, false);

        if (!xml_parse($xmlParser, $newXml, true)) {
            $error = xml_error_string(xml_get_error_code($xmlParser));
            $line  = xml_get_current_line_number($xmlParser);

            throw new Exception("XML Error: {$error} at line {$line}\n");
        }

        xml_parser_free($xmlParser);

        // Diff.
        $leftComparator  = new TextNodeComparator($oldHandler);
        $rightComparator = new TextNodeComparator($newHandler);

        $content = new ChangeText(55);
        $handler = new DelegatingContentHandler($content);
        $output  = new HtmlSaxDiffOutput($handler, 'test');
        $differ  = new HtmlDiffer($output);
        $differ->diff($leftComparator, $rightComparator);

        return strval($content);
    }
}
