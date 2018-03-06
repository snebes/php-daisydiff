<?php

namespace DaisyDiff\Tag;

use PHPUnit\Framework\TestCase;

/**
 * Simple examples for Tag diffing.
 */
class TagDifferTest extends TestCase
{
    /**
     * Adding a single word.
     */
    public function testSimpleTextAdd(): void
    {
        $oldText = '<p> This is a blue book</p>';
        $newText = '<p> This is a big blue book</p>';

        $tagTest = new TagTestFixture();
        $tagTest->performTagDiff($oldText, $newText);

        $this->assertEquals(3, count($tagTest->getResults()));
        $this->assertEquals($oldText, $tagTest->getReconstructedOriginalText());
        $this->assertEquals($newText, $tagTest->getReconstructedModifiedText());
    }

    /**
     * Removing a single word.
     */
    public function testSimpleTextRemove(): void
    {
        $oldText = '<p> This is a blue book</p>';
        $newText = '<p> This is a book</p>';

        $tagTest = new TagTestFixture();
        $tagTest->performTagDiff($oldText, $newText);

        $this->assertEquals(3, count($tagTest->getResults()));
        $this->assertEquals($oldText, $tagTest->getReconstructedOriginalText());
        $this->assertEquals($newText, $tagTest->getReconstructedModifiedText());
    }

    /**
     * Changing a single word.
     */
    public function testSimpleTextChange(): void
    {
        $oldText = '<p> This is a blue book</p>';
        $newText = '<p> This is a green book</p>';

        $tagTest = new TagTestFixture();
        $tagTest->performTagDiff($oldText, $newText);

        $this->assertEquals(4, count($tagTest->getResults()));
        $this->assertEquals($oldText, $tagTest->getReconstructedOriginalText());
        $this->assertEquals($newText, $tagTest->getReconstructedModifiedText());
    }

    /**
     * Adding an HTML attribute.
     */
    public function testSimpleAttributeAdd(): void
    {
        $oldText = '<p> This is a blue book</p>';
        $newText = '<p id="sample"> This is a blue book</p>';

        $tagTest = new TagTestFixture();
        $tagTest->performTagDiff($oldText, $newText);

        $this->assertEquals(4, count($tagTest->getResults()));
        $this->assertEquals($oldText, $tagTest->getReconstructedOriginalText());
        $this->assertEquals($newText, $tagTest->getReconstructedModifiedText());
    }

    /**
     * Adding an HTML tag.
     */
    public function testSimpleTagAdd(): void
    {
        $oldText = '<p> This is a blue book</p>';
        $newText = '<p> This is a <b>blue</b> book</p>';

        $tagTest = new TagTestFixture();
        $tagTest->performTagDiff($oldText, $newText);

        $this->assertEquals(5, count($tagTest->getResults()));
        $this->assertEquals($oldText, $tagTest->getReconstructedOriginalText());
        $this->assertEquals($newText, $tagTest->getReconstructedModifiedText());
    }

    /**
     * Two text changes.
     */
    public function testTwiceChangeText(): void
    {
        $oldText = '<p> This is a blue book</p>';
        $newText = '<p> This is a red table</p>';

        $tagTest = new TagTestFixture();
        $tagTest->performTagDiff($oldText, $newText);

        $this->assertEquals(4, count($tagTest->getResults()));
        $this->assertEquals($oldText, $tagTest->getReconstructedOriginalText());
        $this->assertEquals($newText, $tagTest->getReconstructedModifiedText());
    }

    /**
     * Strange out of bounds exception. See issue 22 in Google code project
     */
    public function testStrangeOBException(): void
    {
        $oldText = '<p>hello</p>';
        $newText = '<p>hello in the end</p><p>New</p>';

        $tagTest = new TagTestFixture();
        $tagTest->performTagDiff($oldText, $newText);

        $this->assertEquals(3, count($tagTest->getResults()));
        $this->assertEquals($oldText, $tagTest->getReconstructedOriginalText());
        $this->assertEquals($newText, $tagTest->getReconstructedModifiedText());
    }
}
