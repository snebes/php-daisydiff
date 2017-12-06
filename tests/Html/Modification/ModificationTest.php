<?php declare(strict_types=1);

namespace DaisyDiff\Html\Modification;

use DaisyDiff\Html\Dom\TagNode;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

/**
 * Modification Tests.
 */
class ModificationTest extends TestCase
{
    /**
     * Test cloning.
     */
    public function testClone(): void
    {
        $newM = new Modification(ModificationType::ADDED, ModificationType::REMOVED);
        $clonedModification = clone $newM;

        $this->assertFalse($newM === $clonedModification);
    }

    /**
     * @covers DaisyDiff\Html\Modification\Modification::getId
     * @covers DaisyDiff\Html\Modification\Modification::setId
     */
    public function testSetId(): void
    {
        $newM = new Modification(ModificationType::ADDED, ModificationType::REMOVED);
        $id   = 123;

        $this->assertEquals(-1, $newM->getId());

        $newM->setId($id);
        $this->assertEquals($id, $newM->getId());
    }

    /**
     * @covers DaisyDiff\Html\Modification\Modification::getType
     * @covers DaisyDiff\Html\Modification\Modification::getOutputType
     */
    public function testGetTypes(): void
    {
        $newM = new Modification(ModificationType::ADDED, ModificationType::REMOVED);

        $this->assertEquals(ModificationType::ADDED, $newM->getType());
        $this->assertEquals(ModificationType::REMOVED, $newM->getOutputType());
    }

    /**
     * @covers DaisyDiff\Html\Modification\Modification::getPrevious
     * @covers DaisyDiff\Html\Modification\Modification::setPrevious
     */
    public function testGetSetPrevious(): void
    {
        $newM = new Modification(ModificationType::ADDED, ModificationType::REMOVED);

        $newM->setPrevious($newM);
        $this->assertEquals($newM, $newM->getPrevious());

        $newM->setPrevious(null);
        $this->assertNull($newM->getPrevious());
    }

    /**
     * @covers DaisyDiff\Html\Modification\Modification::getNext
     * @covers DaisyDiff\Html\Modification\Modification::setNext
     */
    public function testGetSetNext(): void
    {
        $newM = new Modification(ModificationType::ADDED, ModificationType::REMOVED);

        $newM->setNext($newM);
        $this->assertEquals($newM, $newM->getNext());

        $newM->setNext(null);
        $this->assertNull($newM->getNext());
    }

    /**
     * @covers DaisyDiff\Html\Modification\Modification::getChanges
     * @covers DaisyDiff\Html\Modification\Modification::setChanges
     */
    public function testGetSetChanges(): void
    {
        $newM = new Modification(ModificationType::ADDED, ModificationType::REMOVED);
        $changes = '<b>UIC</b>';

        $this->assertNull($newM->getChanges());

        $newM->setChanges($changes);
        $this->assertEquals($changes, $newM->getChanges());
    }

    /**
     * @covers DaisyDiff\Html\Modification\Modification::isFirstOfID
     * @covers DaisyDiff\Html\Modification\Modification::setFirstOfID
     */
    public function testIsFirstOfId(): void
    {
        $newM = new Modification(ModificationType::ADDED, ModificationType::REMOVED);

        $this->assertFalse($newM->isFirstOfID());

        $newM->setFirstOfID(true);
        $this->assertTrue($newM->isFirstOfID());
    }

    /**
     * @covers DaisyDiff\Html\Modification\Modification::getHtmlLayoutChanges
     * @covers DaisyDiff\Html\Modification\Modification::setHtmlLayoutChanges
     */
    public function testGetHtmlLayoutChanges(): void
    {
        $newM = new Modification(ModificationType::ADDED, ModificationType::REMOVED);
        $htmlLayoutChanges = [];

        $this->assertNull($newM->getHtmlLayoutChanges());

        $htmlLayoutChanges[] = new HtmlLayoutChange();
        $newM->setHtmlLayoutChanges($htmlLayoutChanges);
        $this->assertEquals($htmlLayoutChanges, $newM->getHtmlLayoutChanges());
    }
}
