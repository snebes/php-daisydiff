<?php declare(strict_types=1);

namespace DaisyDiff\RangeDifferencer;

use PHPUnit\Framework\TestCase;

/**
 * OldDifferencer Tests.
 */
class OldDifferencerTest extends TestCase
{
    /**
     * Test cloning.
     */
    public function testFindDifferencesExample1(): void
    {
        $oldText = '<p> This is a blue book</p>';
        $newText = '<p> This is a <b>big</b> blue book</p>';

        $this->assertTrue(true);
    }
}
