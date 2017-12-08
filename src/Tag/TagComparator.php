<?php declare(strict_types=1);

namespace DaisyDiff\Tag;

use DaisyDiff\RangeDifferencer\RangeComparatorInterface;
use OutOfBoundsException;
use RuntimeException;

/**
 * Takes a String and generates tokens/atoms that can be used by LCS. This comparator is used specifically for HTML
 * documents.
 */
class TagComparator implements AtomSplitterInterface
{
    /** @var AtomInterface[] */
    private $atoms;

    /**
     * @param  string $s
     */
    public function __construct(?string $s)
    {
        $this->generateAtoms($s ?? '');
    }

    /**
     * @return AtomInterface[]
     */
    public function getAtoms(): iterable
    {
        return $this->atoms ?? [];
    }

    /**
     * @param  string $s
     * @return void
     */
    private function generateAtoms(string $s): void
    {
        if (count($this->atoms) > 0) {
            throw new RuntimeException('Atoms can only be generated once');
        }

        $currentWord = '';

        for ($i = 0; $i < mb_strlen($s); $i++) {
            $c = $s[$i];
            $tag = mb_substr($s, $i, mb_strpos($s, '>', $i) + 1);

            if ($c == '<' && TagAtom::isValidTag($tag)) {
                // A tag.
                if (mb_strlen($currentWord) > 0) {
                    $this->atoms[] = new TextAtom($currentWord);
                    $currentWord = '';
                }

                $end = mb_strpos($s, '>', $i);
                $this->atoms[] = new TagAtom(mb_substr($s, $i, $end + 1));
                $i = $end;
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
                // Something else.
                $currentWord .= $c;
            }
        }

        if (mb_strlen($currentWord) > 0) {
            $this->atoms[] = new TextAtom($currentWord);
        }
    }

    /**
     * @param  int $startAtom
     * @param  int $endAtom
     * @return string
     */
    public function substring(int $startAtom, int $endAtom = -1): string
    {
        if (-1 == $endAtom) {
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

    /**
     * @param  int $i
     * @return AtomInterface
     */
    public function getAtom(int $i): AtomInterface
    {
        if ($i < 0 || $i >= count($this->atoms)) {
            throw new OutOfBoundsException(sprintf('Index: %d, Size: %d', $i, count($this->atoms)));
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
        if (!$other instanceof TagComparator) {
            return false;
        }

        return $other->getAtom($otherIndex)->equalsIdentifier($this->getAtom($thisIndex));
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
}
