<?php
/**
 * (c) Steve Nebes <snebes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace SN\DaisyDiff\Html\Modification;

use PHPUnit\Framework\TestCase;

/**
 * HtmlLayoutChange Tests.
 */
class HtmlLayoutChangeTest extends TestCase
{
    public function testGetSetType(): void
    {
        $tagAdd = new HtmlLayoutChange();
        $tagAdd->setType(HtmlLayoutChange::TAG_ADDED);

        $tagRemoved = new HtmlLayoutChange();
        $tagRemoved->setType(HtmlLayoutChange::TAG_REMOVED);

        $nullTag = new HtmlLayoutChange();
        $nullTag->setType(null);

        $this->assertSame(HtmlLayoutChange::TAG_ADDED, $tagAdd->getType());
        $this->assertSame(HtmlLayoutChange::TAG_REMOVED, $tagRemoved->getType());
        $this->assertNull($nullTag->getType());
    }

    public function testGetSetTag(): void
    {
        $tagAdd = new HtmlLayoutChange();
        $tagAdd->setOpeningTag('<p>');
        $tagAdd->setEndingTag('</p>');

        $nullTag = new HtmlLayoutChange();
        $nullTag->setOpeningTag(null);

        $this->assertSame('<p>', $tagAdd->getOpeningTag());
        $this->assertSame('</p>', $tagAdd->getEndingTag());
        $this->assertNull($nullTag->getOpeningTag());
        $this->assertSame('', $nullTag->getEndingTag());

        $nullTag->setEndingTag(null);
        $this->assertNull($nullTag->getEndingTag());
    }
}
