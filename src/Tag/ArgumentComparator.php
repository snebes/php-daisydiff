<?php
/**
 * (c) Steve Nebes <snebes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace DaisyDiff\Tag;

use DaisyDiff\RangeDifferencer\RangeComparatorInterface;

/**
 * Takes a String and generates tokens/atoms that can be used by LCS. This comparator is used specifically for arguments
 * inside HTML tags.
 */
class ArgumentComparator implements AtomSplitterInterface
{
    /** @var AtomInterface[] */
    private $atoms = [];

    /**
     * Default values.
     *
     * @param string $s
     */
    public function __construct(string $s)
    {
        $this->generateAtoms($s);
    }

    /**
     * @param string $s
     * @throws \RuntimeException
     */
    private function generateAtoms(string $s): void
    {
        if (\count($this->atoms) > 0) {
            throw new \RuntimeException('Atoms can only be split once.');
        }

        $currentWord = '';

        for ($i = 0; $i < \mb_strlen($s); $i++) {
            $c = \mb_substr($s, $i, 1);

            if ($c === '<' || $c === '>') {
                if (\mb_strlen($currentWord) > 0) {
                    $this->atoms[] = new TextAtom($currentWord);
                }

                $this->atoms[] = new TextAtom($c);
                $currentWord = '';
            } elseif (DelimiterAtom::isValidDelimiter($c)) {
                // A delimiter.
                if (\mb_strlen($currentWord) > 0) {
                    $this->atoms[] = new TextAtom($currentWord);
                    $currentWord = '';
                }

                $this->atoms[] = new DelimiterAtom($c);
            } else {
                $currentWord .= $c;
            }
        }

        if (\mb_strlen($currentWord) > 0) {
            $this->atoms[] = new TextAtom($currentWord);
        }
    }

    /**
     * @param int $i
     * @return AtomInterface
     *
     * @throws \OutOfBoundsException
     */
    public function getAtom(int $i): AtomInterface
    {
        if (isset($this->atoms[$i])) {
            return $this->atoms[$i];
        }

        throw new \OutOfBoundsException(\sprintf('Index: %d, Size: %d', $i, \count($this->atoms)));
    }

    /**
     * @return int
     */
    public function getRangeCount(): int
    {
        return \count($this->atoms);
    }

    /**
     * @param int                      $thisIndex
     * @param RangeComparatorInterface $other
     * @param int                      $otherIndex
     * @return bool
     */
    public function rangesEqual(int $thisIndex, RangeComparatorInterface $other, int $otherIndex): bool
    {
        if ($other instanceof ArgumentComparator) {
            return $other->getAtom($otherIndex)->equalsIdentifier($this->getAtom($thisIndex));
        }

        return false;
    }

    /**
     * @param int                      $length
     * @param int                      $maxLength
     * @param RangeComparatorInterface $other
     * @return bool
     */
    public function skipRangeComparison(int $length, int $maxLength, RangeComparatorInterface $other): bool
    {
        return false;
    }

    /**
     * @param int      $startAtom
     * @param int|null $endAtom
     * @return string
     */
    public function substring(int $startAtom, int $endAtom = null): string
    {
        if (null === $endAtom) {
            $endAtom = \count($this->atoms);
        }

        if ($startAtom === $endAtom) {
            return '';
        } else {
            $result = '';

            for ($i = $startAtom; $i < $endAtom; $i++) {
                $result .= $this->atoms[$i]->getFullText();
            }

            return $result;
        }
    }
}
