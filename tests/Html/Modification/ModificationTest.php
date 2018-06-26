<?php

declare(strict_types=1);

namespace DaisyDiff\Html\Modification;

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

        $this->assertFalse($newM === $clonedModification);
    }

    public function testSetId(): void
    {
        $newM = new Modification(ModificationType::ADDED, ModificationType::REMOVED);
        $id   = 123;

        $newM->setId($id);
        $this->assertEquals($id, $newM->getId());
    }

    public function testGetTypes(): void
    {
        $newM = new Modification(ModificationType::ADDED, ModificationType::REMOVED);

        $this->assertEquals(ModificationType::ADDED, $newM->getType());
        $this->assertEquals(ModificationType::REMOVED, $newM->getOutputType());
    }

    public function testGetSetPrevious(): void
    {
        $newM = new Modification(ModificationType::ADDED, ModificationType::REMOVED);

        $newM->setPrevious($newM);
        $this->assertEquals($newM, $newM->getPrevious());

        $newM->setPrevious(null);
        $this->assertNull($newM->getPrevious());
    }

    public function testGetSetNext(): void
    {
        $newM = new Modification(ModificationType::ADDED, ModificationType::REMOVED);

        $newM->setNext($newM);
        $this->assertEquals($newM, $newM->getNext());

        $newM->setNext(null);
        $this->assertNull($newM->getNext());
    }

    public function testGetSetChanges(): void
    {
        $newM = new Modification(ModificationType::ADDED, ModificationType::REMOVED);
        $changes = '<b>UIC</b>';

        $this->assertEmpty($newM->getChanges());

        $newM->setChanges($changes);
        $this->assertEquals($changes, $newM->getChanges());
    }

    public function testIsFirstOfId(): void
    {
        $newM = new Modification(ModificationType::ADDED, ModificationType::REMOVED);

        $this->assertFalse($newM->isFirstOfID());

        $newM->setFirstOfID(true);
        $this->assertTrue($newM->isFirstOfID());
    }

    public function testGetHtmlLayoutChanges(): void
    {
        $newM = new Modification(ModificationType::ADDED, ModificationType::REMOVED);
        $htmlLayoutChanges = [];

        $this->assertEmpty($newM->getHtmlLayoutChanges());

        $htmlLayoutChanges[] = new HtmlLayoutChange();
        $newM->setHtmlLayoutChanges($htmlLayoutChanges);
        $this->assertEquals($htmlLayoutChanges, $newM->getHtmlLayoutChanges());
    }
}
