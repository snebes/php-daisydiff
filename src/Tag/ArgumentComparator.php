<?php

namespace DaisyDiff\Tag;

use DaisyDiff\RangeDifferencer\RangeComparatorInterface;
use Exception;
use OutOfBoundsException;

/**
 * Takes a String and generates tokens/atoms that can be used by LCS. This comparator is used specifically for arguments
 * inside HTML tags.
 */
class ArgumentComparator implements AtomSplitterInterface
{
    /** @var AtomInterface[] */
    private $atoms = [];

    /**
     * @param string $s
     * @throws Exception
     */
    public function __construct(string $s)
    {
        $this->generateAtoms($s);
    }

    /**
     * @param  string $s
     * @return void
     * @throws Exception
     */
    private function generateAtoms(string $s): void
    {
        if (count($this->atoms) > 0) {
            throw new Exception('Atoms can only be split once.');
        }

        $currentWord = '';

        for ($i = 0; $i < mb_strlen($s); $i++) {
            $c = mb_substr($s, $i, 1);

            if ($c == '<' || $c == '>') {
                if (mb_strlen($currentWord) > 0) {
                    $this->atoms[] = new TextAtom($currentWord);

                    $currentWord = '';
                }

                $this->atoms[] = new TextAtom('' . $c);
                $currentWord = '';
            }
            elseif (DelimiterAtom::isValidDelimiter('' . $c)) {
                // A delimiter.
                if (mb_strlen($currentWord) > 0) {
                    $this->atoms[] = new TextAtom($currentWord);
                    $currentWord = '';
                }

                $this->atoms[] = new DelimiterAtom($c);
            }
            else {
                $currentWord .= $c;
            }
        }

        if (mb_strlen($currentWord) > 0) {
            $this->atoms[] = new TextAtom($currentWord);
        }
    }

    /**
     * @param  int $i
     * @return AtomInterface
     * @throws OutOfBoundsException
     */
    public function getAtom(int $i): AtomInterface
    {
        if ($i < 0 || $i >= count($this->atoms)) {
            throw new OutOfBoundsException('There is no Atom with index ' . $i);
        }

        return $this->atoms[$i];
    }

    /**
     * @return int
     */
    public function getRangeCount(): int
    {
        return count($this->atoms);
    }

    /**
     * @param  int                      $thisIndex
     * @param  RangeComparatorInterface $other
     * @param  int                      $otherIndex
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
     * @param  int                      $length
     * @param  int                      $maxLength
     * @param  RangeComparatorInterface $other
     * @return bool
     */
    public function skipRangeComparison(int $length, int $maxLength, RangeComparatorInterface $other): bool
    {
        return false;
    }

    /**
     * @param  int      $startAtom
     * @param  int|null $endAtom
     * @return string
     */
    public function substring(int $startAtom, ?int $endAtom = null): string
    {
        if (is_null($endAtom)) {
            $endAtom = count($this->atoms);
        }

        if ($startAtom == $endAtom) {
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
