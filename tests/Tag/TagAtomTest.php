<?php declare(strict_types=1);
/**
 * (c) Steve Nebes <snebes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DaisyDiff\Tag;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * TagAtom Tests.
 */
class TagAtomTest extends TestCase
{
    public function testTagAtomIdentifiers(): void
    {
        $sample  = '<b id="sample">';
        $example = '<p>';

        $sampleAtom  = new TagAtom($sample);
        $exampleAtom = new TagAtom($example);

        $this->assertEquals('p', $exampleAtom->getIdentifier());
        $this->assertEquals('id="sample"', $sampleAtom->getInternalIdentifiers());
    }

    public function testTagAtomHasInternalIdentifier(): void
    {
        $sample  = '<b id="sample">';
        $example = '<p>';

        $sampleAtom  = new TagAtom($sample);
        $exampleAtom = new TagAtom($example);

        $this->assertTrue($sampleAtom->hasInternalIdentifiers());
        $this->assertFalse($exampleAtom->hasInternalIdentifiers());
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testTagAtomException(): void
    {
        $example  = '<p><b>Hello</b></p>';

        try {
            new TagAtom($example);
        } catch (InvalidArgumentException $e) {
            $this->assertEquals('The given string is not a valid tag.', $e->getMessage());
            throw $e;
        }
    }

    public function testTagAtomGetFullText(): void
    {
        $sample  = '<b id="sample">';
        $example = '<p>';

        $sampleAtom  = new TagAtom($sample);
        $exampleAtom = new TagAtom($example);

        $this->assertEquals($sample, $sampleAtom->getFullText());
        $this->assertEquals($example, $exampleAtom->getFullText());
    }

    public function testToString(): void
    {
        $sample = '<b id="sample">';
        $output = "TagAtom: {$sample}";

        $sampleAtom = new TagAtom($sample);

        $this->assertEquals($output, strval($sampleAtom));
    }

    public function testEqualsIdentifier(): void
    {
        $sample  = '<b id="sample">';
        $example = '<p>';

        $sampleAtom  = new TagAtom($sample);
        $exampleAtom = new TagAtom($example);

        $this->assertFalse($sampleAtom->equalsIdentifier($exampleAtom));
        $this->assertTrue($exampleAtom->equalsIdentifier($exampleAtom));
    }
}
