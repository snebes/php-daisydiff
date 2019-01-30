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
 * Modification Tests.
 */
class ModificationTest extends TestCase
{
    public function testClone(): void
    {
        $newM = new Modification(ModificationType::ADDED, ModificationType::REMOVED);
        $clonedModification = clone $newM;

        $this->assertNotSame($clonedModification, $newM);
        $this->assertEquals($clonedModification, $newM);
    }

    public function testSetId(): void
    {
        $newM = new Modification(ModificationType::ADDED, ModificationType::REMOVED);
        $id   = 123;

        $newM->setId($id);
        $this->assertSame($id, $newM->getId());
    }

    public function testGetTypes(): void
    {
        $newM = new Modification(ModificationType::ADDED, ModificationType::REMOVED);

        $this->assertSame(ModificationType::ADDED, $newM->getType());
        $this->assertSame(ModificationType::REMOVED, $newM->getOutputType());
    }

    public function testGetSetPrevious(): void
    {
        $newM = new Modification(ModificationType::ADDED, ModificationType::REMOVED);

        $newM->setPrevious($newM);
        $this->assertSame($newM, $newM->getPrevious());

        $newM->setPrevious(null);
        $this->assertNull($newM->getPrevious());
    }

    public function testGetSetNext(): void
    {
        $newM = new Modification(ModificationType::ADDED, ModificationType::REMOVED);

        $newM->setNext($newM);
        $this->assertSame($newM, $newM->getNext());

        $newM->setNext(null);
        $this->assertNull($newM->getNext());
    }

    public function testGetSetChanges(): void
    {
        $newM = new Modification(ModificationType::ADDED, ModificationType::REMOVED);
        $changes = '<b>UIC</b>';

        $this->assertSame('', $newM->getChanges());

        $newM->setChanges($changes);
        $this->assertSame($changes, $newM->getChanges());
    }

    public function testIsFirstOfId(): void
    {
        $newM = new Modification(ModificationType::ADDED, ModificationType::REMOVED);

        $this->assertFalse($newM->isFirstOfId());

        $newM->setFirstOfID(true);
        $this->assertTrue($newM->isFirstOfId());
    }

    public function testGetHtmlLayoutChanges(): void
    {
        $newM = new Modification(ModificationType::ADDED, ModificationType::REMOVED);
        $htmlLayoutChanges = [];

        $this->assertEmpty($newM->getHtmlLayoutChanges());

        $htmlLayoutChanges[] = new HtmlLayoutChange();
        $newM->setHtmlLayoutChanges($htmlLayoutChanges);

        $this->assertSame($htmlLayoutChanges, $newM->getHtmlLayoutChanges());
    }
}
