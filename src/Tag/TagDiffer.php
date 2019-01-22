<?php
/**
 * (c) Steve Nebes <snebes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace DaisyDiff\Tag;

use DaisyDiff\Output\TextDifferInterface;
use DaisyDiff\Output\TextDiffOutputInterface;
use DaisyDiff\RangeDifferencer\RangeDifference;
use DaisyDiff\RangeDifferencer\RangeDifferencer;

/**
 * Takes 2 AtomSplitters and computes the difference between them. Output is sent to a given HTMLSaxDiffOutput and tags
 * are diffed internally on a second iteration. The results are processed as to combine small subsequent changes in to
 * larger changes.
 */
class TagDiffer implements TextDifferInterface
{
    /** @var TextDiffOutputInterface */
    private $output;

    /**
     * Default values.
     *
     * @param TextDiffOutputInterface $output
     */
    public function __construct(TextDiffOutputInterface $output)
    {
        $this->output = $output;
    }

    /**
     * @param AtomSplitterInterface $leftComparator
     * @param AtomSplitterInterface $rightComparator
     */
    public function diff(AtomSplitterInterface $leftComparator, AtomSplitterInterface $rightComparator): void
    {
        /** @var RangeDifference[] $differences */
        $differences = RangeDifferencer::findDifferences($leftComparator, $rightComparator);
        /** @var RangeDifference[] $pDifferences */
        $pDifferences = $this->preProcess($differences, $leftComparator);

        $rightAtom = 0;
        $leftAtom = 0;

        for ($i = 0, $iMax = \count($pDifferences); $i < $iMax; $i++) {
            $this->parseNoChange(
                $leftAtom, $pDifferences[$i]->getLeftStart(),
                $rightAtom, $pDifferences[$i]->getRightStart(),
                $leftComparator, $rightComparator);

            $leftString = $leftComparator->substring($pDifferences[$i]->getLeftStart(), $pDifferences[$i]->getLeftEnd());
            $rightString = $rightComparator->substring($pDifferences[$i]->getRightStart(), $pDifferences[$i]->getRightEnd());

            if ($pDifferences[$i]->getLeftLength() > 0) {
                $this->output->addRemovedPart($leftString);
            }

            if ($pDifferences[$i]->getRightLength() > 0) {
                $this->output->addAddedPart($rightString);
            }

            $rightAtom = $pDifferences[$i]->getRightEnd();
            $leftAtom = $pDifferences[$i]->getLeftEnd();
        }

        if ($rightAtom < $rightComparator->getRangeCount()) {
            $this->parseNoChange(
                $leftAtom, $leftComparator->getRangeCount(),
                $rightAtom, $rightComparator->getRangeCount(),
                $leftComparator, $rightComparator);
        }
    }

    /**
     * @param int                   $beginLeft
     * @param int                   $endLeft
     * @param int                   $beginRight
     * @param int                   $endRight
     * @param AtomSplitterInterface $leftComparator
     * @param AtomSplitterInterface $rightComparator
     * @throws
     */
    private function parseNoChange(
        int $beginLeft,
        int $endLeft,
        int $beginRight,
        int $endRight,
        AtomSplitterInterface $leftComparator,
        AtomSplitterInterface $rightComparator
    ): void {
        // $endRight is not used below.
        \assert(\is_int($endRight));

        $s = '';

        // We can assume that the LCS is correct and that there are exactly as many atoms left and right.
        while ($beginLeft < $endLeft) {
            while ($beginLeft < $endLeft &&
                !$rightComparator->getAtom($beginRight)->hasInternalIdentifiers() &&
                !$leftComparator->getAtom($beginLeft)->hasInternalIdentifiers()) {
                $s .= $rightComparator->getAtom($beginRight)->getFullText();
                $beginRight++;
                $beginLeft++;
            }

            if (\mb_strlen($s) > 0) {
                $this->output->addClearPart($s);
                $s = '';
            }

            if ($beginLeft < $endLeft) {
                $leftComparator2 = new ArgumentComparator($leftComparator->getAtom($beginLeft)->getFullText());
                $rightComparator2 = new ArgumentComparator($rightComparator->getAtom($beginRight)->getFullText());

                /** @var RangeDifference[] $differences2 */
                $differences2 = RangeDifferencer::findDifferences($leftComparator2, $rightComparator2);
                /** @var RangeDifference[] $pDifferences2 */
                $pDifferences2 = $this->preProcess2($differences2, 2);

                $rightAtom2 = 0;

                for ($j = 0, $jMax = \count($pDifferences2); $j < $jMax; $j++) {
                    if ($rightAtom2 < $pDifferences2[$j]->getRightStart()) {
                        $this->output->addClearPart($rightComparator2->substring(
                            $rightAtom2,
                            $pDifferences2[$j]->getRightStart()));
                    }

                    if ($pDifferences2[$j]->getLeftLength() > 0) {
                        $this->output->addRemovedPart($leftComparator2->substring(
                            $pDifferences2[$j]->getLeftStart(),
                            $pDifferences2[$j]->getLeftEnd()));
                    }

                    if ($pDifferences2[$j]->getRightLength() > 0) {
                        $this->output->addAddedPart($rightComparator2->substring(
                            $pDifferences2[$j]->getRightStart(),
                            $pDifferences2[$j]->getRightEnd()));
                    }

                    $rightAtom2 = $pDifferences2[$j]->getRightEnd();
                }

                if ($rightAtom2 < $rightComparator2->getRangeCount()) {
                    $this->output->addClearPart($rightComparator2->substring($rightAtom2));
                }

                $beginLeft++;
                $beginRight++;
            }
        }
    }

    /**
     * @param RangeDifference[]     $differences
     * @param AtomSplitterInterface $leftComparator
     * @return RangeDifference[]
     *
     * @throws \RuntimeException
     */
    private function preProcess(array $differences, AtomSplitterInterface $leftComparator): array
    {
        $newRanges = [];

        for ($i = 0, $iMax = \count($differences); $i < $iMax; $i++) {
            $leftStart = $differences[$i]->getLeftStart();
            $leftEnd = $differences[$i]->getLeftEnd();
            $rightStart = $differences[$i]->getRightStart();
            $rightEnd = $differences[$i]->getRightEnd();
            $kind = $differences[$i]->getKind();
            $temp = $leftEnd;
            $connecting = true;

            while ($connecting && $i + 1 < $iMax && $differences[$i + 1]->getKind() === $kind) {
                $bridgeLength = 0;
                $numTokens = \max($leftEnd - $leftStart, $rightEnd - $rightStart);

                if ($numTokens > 5) {
                    if ($numTokens > 10) {
                        $bridgeLength = 3;
                    } else {
                        $bridgeLength = 2;
                    }
                }

                while ($temp < $differences[$i + 1]->getLeftStart() &&
                    ($leftComparator->getAtom($temp) instanceof DelimiterAtom || ($bridgeLength-- > 0))) {
                    $temp++;
                }

                if ($temp === $differences[$i + 1]->getLeftStart()) {
                    $leftEnd = $differences[$i + 1]->getLeftEnd();
                    $rightEnd = $differences[$i + 1]->getRightEnd();
                    $temp = $leftEnd;
                    $i++;
                } else {
                    $connecting = false;

                    if (!$leftComparator->getAtom($temp) instanceof DelimiterAtom) {
                        if (' ' === $leftComparator->getAtom($temp)->getFullText()) {
                            throw new \RuntimeException('Space found.');
                        }
                    }
                }
            }

            $newRanges[] = new RangeDifference(
                $kind,
                $rightStart, $rightEnd - $rightStart,
                $leftStart, $leftEnd - $leftStart);
        }

        return $newRanges;
    }

    /**
     * @param RangeDifference[] $differences
     * @param int               $span
     * @return RangeDifference[]
     */
    private function preProcess2(array $differences, int $span): array
    {
        $newRanges = [];

        for ($i = 0, $iMax = \count($differences); $i < $iMax; $i++) {
            $leftStart = $differences[$i]->getLeftStart();
            $leftEnd = $differences[$i]->getLeftEnd();
            $rightStart = $differences[$i]->getRightStart();
            $rightEnd = $differences[$i]->getRightEnd();
            $kind = $differences[$i]->getKind();

            while ($i + 1 < $iMax &&
                $differences[$i + 1]->getKind() === $kind &&
                $differences[$i + 1]->getLeftStart() <= $leftEnd + $span &&
                $differences[$i + 1]->getRightStart() <= $rightEnd + $span) {
                $leftEnd = $differences[$i + 1]->getLeftEnd();
                $rightEnd = $differences[$i + 1]->getRightEnd();
                $i++;
            }

            $newRanges[] = new RangeDifference(
                $kind,
                $rightStart, $rightEnd - $rightStart,
                $leftStart, $leftEnd - $leftStart);
        }

        return $newRanges;
    }
}
