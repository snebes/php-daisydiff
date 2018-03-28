<?php declare(strict_types=1);

namespace DaisyDiff\Html\Modification;

use PHPUnit\Framework\TestCase;

/**
 * HtmlLayoutChange Tests.
 */
class HtmlLayoutChangeTest extends TestCase
{
    public function testGetSetType(): void
    {
        $tagAdd = new HtmlLayoutChange();
        $tagAdd->setType(HtmlLayoutChangeType::TAG_ADDED);

        $tagRemoved = new HtmlLayoutChange();
        $tagRemoved->setType(HtmlLayoutChangeType::TAG_REMOVED);

        $nullTag = new HtmlLayoutChange();
        $nullTag->setType(null);

        $this->assertEquals(HtmlLayoutChangeType::TAG_ADDED, $tagAdd->getType());
        $this->assertEquals(HtmlLayoutChangeType::TAG_REMOVED, $tagRemoved->getType());
        $this->assertNull($nullTag->getType());
    }

    public function testGetSetTag(): void
    {
        $tagAdd = new HtmlLayoutChange();
        $tagAdd->setOpeningTag('<p>');
        $tagAdd->setEndingTag('</p>');

        $nullTag = new HtmlLayoutChange();
        $nullTag->setOpeningTag(null);

        $this->assertEquals('<p>', $tagAdd->getOpeningTag());
        $this->assertEquals('</p>', $tagAdd->getEndingTag());
        $this->assertNull($nullTag->getOpeningTag());
        $this->assertEquals('', $nullTag->getEndingTag());

        $nullTag->setEndingTag(null);
        $this->assertNull($nullTag->getEndingTag());
    }
}
