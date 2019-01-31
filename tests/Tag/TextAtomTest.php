<?php
/**
 * (c) Steve Nebes <snebes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace SN\DaisyDiff\Tag;

use PHPUnit\Framework\TestCase;

/**
 * TextAtom Tests.
 */
class TextAtomTest extends TestCase
{
    public function testIsValidAtom1(): void
    {
        $input = '<p>This is a blue book</p>';
        $empty = '';
        $lengthOne = '(';

        $atom = new TextAtom($input);

        $this->assertTrue($atom->isValidAtom($input));
        $this->assertFalse($atom->isValidAtom($empty));
        $this->assertTrue($atom->isValidAtom($lengthOne));
    }

    public function testIsValidAtom2(): void
    {
        $input = '<p>This is a blue book</p>';
        $delimInput = '&';
        $empty = '';

        $atom = new TextAtom("' '");

        $this->assertTrue($atom->isValidAtom($input));
        $this->assertTrue($atom->isValidAtom($delimInput));
        $this->assertFalse($atom->isValidAtom($empty));
    }

    /**
     * @expectedException \Exception
     */
    public function testIsValidAtomException(): void
    {
        try {
            new TextAtom('');
        } catch (\Exception $e) {
            $this->assertInstanceOf(\RuntimeException::class, $e);

            throw $e;
        }
    }

    public function testEqualsIdentifier(): void
    {
        $input = '<p>This is a blue book</p>';
        $atom = new TextAtom($input);

        $matchInput = '<p>This is a blue book</p>';
        $matchAtom = new TextAtom($matchInput);

        $empty = ' ';
        $emptyAtom = new TextAtom($empty);

        $differentInput = "<b>This is a different \n input</b>";
        $differentAtom = new TextAtom($differentInput);

        $this->assertTrue($atom->equalsIdentifier($matchAtom));
        $this->assertFalse($atom->equalsIdentifier($emptyAtom));
        $this->assertFalse($matchAtom->equalsIdentifier($differentAtom));
    }

    public function testToString(): void
    {
        $input = '~';
        $atom = new TextAtom($input);

        $this->assertSame('TextAtom: ~', strval($atom));
    }

    public function testTagAtomIdentifiers(): void
    {
        $example = '<p>';
        $exampleAtom = new TextAtom($example);

        $this->assertSame('<p>', $exampleAtom->getIdentifier());
    }

    /**
     * @expectedException \Exception
     */
    public function testTagAtomInternalIdentifiers(): void
    {
        $example = '<p id="sample">Hello</p>';
        $exampleAtom = new TextAtom($example);

        $this->assertFalse($exampleAtom->hasInternalIdentifiers());

        try {
            $exampleAtom->getInternalIdentifiers();
        } catch (\Exception $e) {
            $this->assertInstanceOf(\RuntimeException::class, $e);

            throw $e;
        }
    }
}
