<?php declare(strict_types=1);

namespace DaisyDiff\Html;

use DaisyDiff\Html\Ancestor\ChangeText;
use DaisyDiff\Html\Dom\DomTreeBuilder;
use DaisyDiff\Html\Dom\ImageNode;
use DaisyDiff\Html\Dom\TagNode;
use DaisyDiff\Html\Dom\TextNode;
use DaisyDiff\Html\Modification\Modification;
use DaisyDiff\Html\Modification\ModificationType;
use Exception;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * HtmlSaxDiffOutput Tests.
 */
class HtmlSaxDiffOutputTest extends TestCase
{
    /** @var ChangeText */
    private $content;

    /**
     * @return HtmlSaxDiffOutput
     */
    private function getOutput(): HtmlSaxDiffOutput
    {
        $this->content = new ChangeText(55);
        $handler = new DelegatingContentHandler($this->content);
        $output  = new HtmlSaxDiffOutput($handler, 'diff');

        return $output;
    }

    /**
     * @param  object $instance
     * @param  string $name
     * @param  array  $params
     * @return mixed
     * @throws
     */
    private function executeMethod($instance, string $name, array $params)
    {
        $refClass = new ReflectionClass($instance);
        $method   = $refClass->getMethod($name);
        $method->setAccessible(true);

        if ('addAttributes' == $name) {
            $mod   = $params[0];
            $attrs = $params[1];
            $method->invokeArgs($instance, [$mod, &$attrs]);

            return $attrs;
        } else {
            $method->invokeArgs($instance, $params);

            return $params[0];
        }
    }

    /**
     * @param  string            $oldText
     * @param  string            $newText
     * @param  HtmlSaxDiffOutput $output
     * @return string
     * @throws Exception
     */
    private function diff(string $oldText, string $newText, HtmlSaxDiffOutput $output): string
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

        $differ = new HtmlDiffer($output);
        $differ->diff($leftComparator, $rightComparator);

        return strval($this->content);
    }

    /**
     * @throws Exception
     */
    public function testGenerateOutputExample1(): void
    {
        $html = new TagNode(null, 'html');
        $body = new TagNode($html, 'body');
        $html->addChild($body);

        $img = new TagNode($body, 'img');
        $body->addChild($img);

        $textImage = new TextNode($img, 'contents of image node');
        $img->addChild($textImage);

        $output = $this->getOutput();
        $output->generateOutput($html);

        $oldText = '<p> This is a blue book</p>';
        $newText = '<p> This is a blue book</p>';
        $result  = $this->diff($oldText, $newText, $output);

        $this->assertContains($textImage->getText(), $result);
        $this->assertContains($newText, $result);
    }

    /**
     * @throws Exception
     */
    public function testGenerateOutputExample2(): void
    {
        $attrs = ['span' => 'diff-tag-html'];

        $html = new TagNode(null, 'html', $attrs);
        $textImage = new TextNode($html, 'contents of html page');
        $html->addChild($textImage);

        $m = new Modification(ModificationType::ADDED, ModificationType::ADDED);
        $m->setFirstOfId(true);
        $textImage->setModification($m);

        $output = $this->getOutput();
        $output->generateOutput($html);

        $oldText = '<p> This is a blue book</p>';
        $newText = '<p> This is a big blue book</p>';
        $result  = $this->diff($oldText, $newText, $output);

        $this->assertContains('span="diff-tag-html"', $result);
        $this->assertContains('<ins class="diff-html-added"', $result);
        $this->assertContains('contents of html page', $result);
    }

    /**
     * @throws Exception
     */
    public function testGenerateOutputExample3(): void
    {
        $html = new TagNode(null, 'html');
        $textImage = new TextNode($html, 'contents of html page');
        $html->addChild($textImage);

        $m = new Modification(ModificationType::CONFLICT, ModificationType::CONFLICT);
        $m->setFirstOfId(true);
        $textImage->setModification($m);

        $output = $this->getOutput();
        $output->generateOutput($html);

        $oldText = '<p> This is a blue book</p>';
        $newText = '<p> This is a big blue book</p>';
        $result  = $this->diff($oldText, $newText, $output);

        $this->assertContains('contents of html page', $result);
        $this->assertContains('<span class="diff-html-conflict"', $result);
        $this->assertContains('<ins class="diff-html-added"', $result);
    }

    /**
     * @throws Exception
     */
    public function testGenerateOutputExample4(): void
    {
        $html = new TagNode(null, 'html');
        $textImage = new TextNode($html, 'contents of html page');
        $html->addChild($textImage);

        $m = new Modification(ModificationType::REMOVED, ModificationType::REMOVED);
        $m->setFirstOfId(true);
        $textImage->setModification($m);

        $output = $this->getOutput();
        $output->generateOutput($html);

        $oldText = '<p> This is a big blue book</p>';
        $newText = '<p> This is a blue book</p>';
        $result  = $this->diff($oldText, $newText, $output);

        $this->assertContains('contents of html page', $result);
        $this->assertContains('<del class="diff-html-removed" id="removed-diff--1" previous="first-diff" changeId="removed-diff--1" next="last-diff"', $result);
    }

    /**
     * @throws Exception
     */
    public function testGenerateOutputExample5(): void
    {
        $html = new TagNode(null, 'html');
        $textImage = new TextNode($html, 'contents of html page');
        $html->addChild($textImage);

        $m = new Modification(ModificationType::CHANGED, ModificationType::CONFLICT);
        $textImage->setModification($m);

        $output = $this->getOutput();
        $output->generateOutput($html);

        $oldText = '<p> This is a blue book</p>';
        $newText = '<p id="sample"> This is a blue book</p>';
        $result  = $this->diff($oldText, $newText, $output);

        $this->assertContains('contents of html page', $result);
        $this->assertContains('<span class="diff-html-changed"', $result);
    }

    /**
     * @throws Exception
     */
    public function testGenerateOutputExample6(): void
    {
        $attrs = ['src' => 'diff-tag-html'];

        $html = new TagNode(null, 'html');
        $textImage = new ImageNode($html, $attrs);
        $html->addChild($textImage);

        $m = new Modification(ModificationType::CHANGED, ModificationType::REMOVED);
        $textImage->setModification($m);

        $output = $this->getOutput();
        $output->generateOutput($html);

        $oldText = '<p> This is a blue book</p>';
        $newText = '<p id="sample"> This is a blue book</p>';
        $result  = $this->diff($oldText, $newText, $output);

        $this->assertContains('<img src="diff-tag-html"', $result);
        $this->assertContains('<del class="diff-html-removed"', $result);
        $this->assertContains('<span class="diff-html-changed"', $result);
        $this->assertContains('<p id="sample">', $result);
    }

    /**
     * @throws Exception
     */
    public function testGenerateOutputExample7(): void
    {
        $attrs = ['src' => 'diff-tag-html'];

        $html = new TagNode(null, 'html');
        $textImage = new ImageNode($html, $attrs);
        $html->addChild($textImage);

        $m = new Modification(ModificationType::NONE, ModificationType::NONE);
        $textImage->setModification($m);

        $output = $this->getOutput();
        $output->generateOutput($html);

        $oldText = '<p> This is a blue book</p>';
        $newText = '<p> This is a blue book</p>';
        $result  = $this->diff($oldText, $newText, $output);

        $this->assertContains('<img src="diff-tag-html"', $result);
        $this->assertContains($oldText, $result);
    }

    /**
     * @throws Exception
     */
    public function testGenerateOutputExample8(): void
    {
        $attrs = ['src' => 'diff-tag-html'];

        $html = new TagNode(null, 'html');
        $textImage = new ImageNode($html, $attrs);
        $html->addChild($textImage);

        $m = new Modification(ModificationType::ADDED, ModificationType::CONFLICT);
        $textImage->setModification($m);

        $output = $this->getOutput();
        $output->generateOutput($html);

        $oldText = '<p> This is a blue book</p>';
        $newText = '<p> This is a big blue book</p>';
        $result  = $this->diff($oldText, $newText, $output);

        $this->assertContains('<img src="diff-tag-html" changeType="diff-conflict-image"', $result);
        $this->assertContains('<span class="diff-html-conflict"', $result);
        $this->assertContains('<ins class="diff-html-added"', $result);
    }

    /**
     * @throws Exception
     */
    public function testGenerateOutputExample9(): void
    {
        $attrs = ['src' => 'diff-tag-html'];

        $html = new TagNode(null, 'html');
        $textImage = new ImageNode($html, $attrs);
        $html->addChild($textImage);

        $previous = new Modification(ModificationType::REMOVED, ModificationType::REMOVED);
        $m = new Modification(ModificationType::ADDED, ModificationType::ADDED);
        $m->setPrevious($previous);
        $textImage->setModification($m);

        $output = $this->getOutput();
        $output->generateOutput($html);

        $oldText = '<p> This is a blue book</p>';
        $newText = '<p> This is a big book</p>';
        $result  = $this->diff($oldText, $newText, $output);

        $this->assertContains('<img src="diff-tag-html" changeType="diff-added-image"', $result);
        $this->assertContains('<ins class="diff-html-added"', $result);
        $this->assertContains('<del class="diff-html-removed"', $result);
    }

    /**
     * @throws Exception
     */
    public function testGenerateOutputExample10(): void
    {
        $attrs = ['src' => 'diff-tag-html'];

        $html = new TagNode(null, 'html');
        $textImage = new ImageNode($html, $attrs);
        $html->addChild($textImage);

        $previous = new Modification(ModificationType::REMOVED, ModificationType::REMOVED);
        $m = new Modification(ModificationType::ADDED, ModificationType::ADDED);
        $m->setPrevious($previous);
        $textImage->setModification($m);

        $output = $this->getOutput();
        $output->generateOutput($html);

        $oldText = '<p> This is a blue book</p>';
        $newText = '<p> This is a big book</p>';
        $result  = $this->diff($oldText, $newText, $output);

        $this->assertContains('<img src="diff-tag-html" changeType="diff-added-image"', $result);
        $this->assertContains('<ins class="diff-html-added"', $result);
        $this->assertContains('<del class="diff-html-removed"', $result);
    }

    public function testAddAttributeExample1(): void
    {
        $attrs = ['src' => 'diff-tag-html'];

        $m = new Modification(ModificationType::CHANGED, ModificationType::CHANGED);

        $output = $this->getOutput();
        $result = $this->executeMethod($output, 'addAttributes', [$m, $attrs]);

        $this->assertTrue(array_key_exists('src', $result));
        $this->assertTrue(array_key_exists('changes', $result));
    }

    public function testAddAttributeExample2(): void
    {
        $attrs = ['src' => 'diff-tag-html'];

        $m = new Modification(ModificationType::ADDED, ModificationType::ADDED);

        $output = $this->getOutput();
        $result = $this->executeMethod($output, 'addAttributes', [$m, $attrs]);

        $this->assertTrue(array_key_exists('src', $result));
        $this->assertTrue(array_key_exists('previous', $result));
    }

    public function testAddAttributeExample3(): void
    {
        $attrs = ['src' => 'diff-tag-html'];

        $m = new Modification(ModificationType::REMOVED, ModificationType::REMOVED);
        $next = new Modification(ModificationType::ADDED, ModificationType::ADDED);
        $m->setPrevious($next);

        $output = $this->getOutput();
        $result = $this->executeMethod($output, 'addAttributes', [$m, $attrs]);

        $this->assertTrue(array_key_exists('src', $result));
        $this->assertTrue(array_key_exists('previous', $result));
    }

    public function testAddAttributeExample4(): void
    {
        $attrs = ['src' => 'diff-tag-html'];

        $m = new Modification(ModificationType::REMOVED, ModificationType::REMOVED);
        $next = new Modification(ModificationType::ADDED, ModificationType::ADDED);
        $m->setNext($next);

        $output = $this->getOutput();
        $result = $this->executeMethod($output, 'addAttributes', [$m, $attrs]);

        $this->assertTrue(array_key_exists('src', $result));
        $this->assertTrue(array_key_exists('previous', $result));
        $this->assertTrue(array_key_exists('changeId', $result));
        $this->assertTrue(array_key_exists('next', $result));
    }
}
