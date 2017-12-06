<?php declare(strict_types=1);

namespace DaisyDiff\Html\Modification;

use DaisyDiff\Html\Dom\TagNode;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

/**
 * HtmlLayoutChange Tests.
 */
class HtmlLayoutChangeTest extends TestCase
{
    /**
     * @covers DaisyDiff\Html\Modification\HtmlLayoutChange::getType
     * @covers DaisyDiff\Html\Modification\HtmlLayoutChange::setType
     */
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

    /**
     * @covers DaisyDiff\Html\Modification\HtmlLayoutChange::getOpeningTag
     * @covers DaisyDiff\Html\Modification\HtmlLayoutChange::setOpeningTag
     * @covers DaisyDiff\Html\Modification\HtmlLayoutChange::getEndingTag
     * @covers DaisyDiff\Html\Modification\HtmlLayoutChange::setEndingTag
     */
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
