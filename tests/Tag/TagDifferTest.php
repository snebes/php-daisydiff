<?php

namespace DaisyDiff\Tag;

use PHPUnit\Framework\TestCase;

/**
 * Simple examples for Tag diffing.
 */
class TagDifferTest extends TestCase
{
    public function testSimpleAddText(): void
    {
        $oldText = '<p> This is a blue book</p>';
        $newText = '<p> This is a big blue book</p>';

        $tagTest = new TagTestFixture();
        $tagTest->performTagDiff($oldText, $newText);

        $this->assertEquals(3, count($tagTest->getResults()));
        $this->assertEquals($oldText, $tagTest->getReconstructedOriginalText());
        $this->assertEquals($newText, $tagTest->getReconstructedModifiedText());
    }
}
