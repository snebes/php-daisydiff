<?php
/**
 * (c) Steve Nebes <snebes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace DaisyDiff\Tag;

use PHPUnit\Framework\TestCase;
use RuntimeException;

/**
 * DelimiterAtom Tests.
 */
class DelimiterAtomTest extends TestCase
{
    public function testIsValidDelimiter(): void
    {
        $delimInput = '&<p>This is a blue book</p>';
        $input = '<p>This is a blue book</p>';
        $empty = null;
        $lengthOne = '(';
        $ch = '!';
        $notDelim = '~';

        $delimiterAtom = new DelimiterAtom(' ');

        $this->assertTrue($delimiterAtom->isValidDelimiter($ch));
        $this->assertFalse($delimiterAtom->isValidDelimiter($delimInput));
        $this->assertFalse($delimiterAtom->isValidDelimiter($input));
        $this->assertFalse($delimiterAtom->isValidDelimiter($empty));
        $this->assertTrue($delimiterAtom->isValidDelimiter($lengthOne));
        $this->assertFalse($delimiterAtom->isValidDelimiter($notDelim));
    }

    public function testIsValidAtom(): void
    {
        $input = '<p>This is a blue book</p>';
        $delimInput = '&';
        $empty = '';

        $delimiterAtom = new DelimiterAtom(' ');

        $this->assertFalse($delimiterAtom->isValidAtom($input));
        $this->assertTrue($delimiterAtom->isValidAtom($delimInput));
        $this->assertFalse($delimiterAtom->isValidAtom($empty));
    }

    /**
     * @expectedException RuntimeException
     */
    public function testIsValidAtomException(): void
    {
        try {
            new DelimiterAtom('~');
        } catch (RuntimeException $e) {
            $this->assertEquals('The given String is not a valid Text Atom.', $e->getMessage());
            throw $e;
        }
    }

    public function testEqualsIdentifier(): void
    {
        $input = '?';
        $atom  = new DelimiterAtom($input);

        $matchInput = '?';
        $matchAtom  = new DelimiterAtom($matchInput);

        $empty = ' ';
        $emptyAtom = new DelimiterAtom($empty);

        $differentInput = "<b>This is a different \n input</b>";
        $differentAtom  = new TextAtom($differentInput);

        $this->assertTrue($atom->equalsIdentifier($matchAtom));
        $this->assertFalse($atom->equalsIdentifier($emptyAtom));
        $this->assertFalse($matchAtom->equalsIdentifier($differentAtom));
    }

    public function testToString(): void
    {
        $input = ';';
        $atom  = new DelimiterAtom($input);

        $this->assertEquals('DelimiterAtom: ;', strval($atom));
    }
}
