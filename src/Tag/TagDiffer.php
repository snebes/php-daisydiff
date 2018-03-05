<?php

namespace DaisyDiff\Tag;

use DaisyDiff\Output\TextDifferInterface;
use DaisyDiff\Output\TextDiffOutputInterface;
use DaisyDiff\RangeDifferencer\RangeDifference;
use DaisyDiff\RangeDifferencer\RangeDifferencer;
use Exception;

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
     * @param TextDiffOutputInterface $output
     */
    public function __construct(TextDiffOutputInterface $output)
    {
        $this->output = $output;
    }

    /**
     * @param  AtomSplitterInterface $leftComparator
     * @param  AtomSplitterInterface $rightComparator
     * @throws Exception
     */
    public function diff(AtomSplitterInterface $leftComparator, AtomSplitterInterface $rightComparator): void
    {
        $differences  = RangeDifferencer::findDifferences($leftComparator, $rightComparator);
        $pDifferences = $this->preProcess($differences, $leftComparator);

        $rightAtom = 0;
        $leftAtom  = 0;

        for ($i = 0; $i < count($pDifferences); $i++) {
            $this->parseNoChange(
                $leftAtom, $pDifferences[$i]->leftStart(),
                $rightAtom, $pDifferences[$i]->rightStart(),
                $leftComparator, $rightComparator);

            $leftString  = $leftComparator->substring($pDifferences[$i]->leftStart(), $pDifferences[$i]->leftEnd());
            $rightString = $rightComparator->substring($pDifferences[$i]->rightStart(), $pDifferences[$i]->rightEnd());

            if ($pDifferences[$i]->leftLength() > 0) {
                $this->output->addRemovedPart($leftString);
            }

            if ($pDifferences[$i]->rightLength() > 0) {
                $this->output->addAddedPart($rightString);
            }

            $rightAtom = $pDifferences[$i]->rightEnd();
            $leftAtom  = $pDifferences[$i]->leftEnd();
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
     */
    private function parseNoChange(
        int $beginLeft,
        int $endLeft,
        int $beginRight,
        int $endRight,
        AtomSplitterInterface $leftComparator,
        AtomSplitterInterface $rightComparator
    ): void {
        $s = '';

        // We can assume that the LCS is correct and that there are exactly as many atoms left and right.
        while ($beginLeft < $endLeft &&
            !$rightComparator->getAtom($beginRight)->hasInternalIdentifiers() &&
            !$leftComparator->getAtom($beginLeft)->hasInternalIdentifiers()) {
            $s .= $rightComparator->getAtom($beginRight)->getFullText();
            $beginRight++;
            $beginLeft++;
        }

        if (mb_strlen($s) > 0) {
            $this->output->addClearPart($s);
        }

        if ($beginLeft < $endLeft) {
            $leftComparator2  = new ArgumentComparator($leftComparator->getAtom($beginLeft)->getFullText());
            $rightComparator2 = new ArgumentComparator($rightComparator->getAtom($beginRight)->getFullText());

            $differences2  = RangeDifferencer::findDifferences($leftComparator2, $rightComparator2);
            $pDifferences2 = $this->preProcess2($differences2, 2);

            $rightAtom2 = 0;

            for ($j = 0; $j < count($pDifferences2); $j++) {
                if ($rightAtom2 < $pDifferences2[$j]->rightStart()) {
                    $this->output->addClearPart($rightComparator2->substring(
                        $rightAtom2,
                        $pDifferences2[$j]->rightStart()));
                }

                if ($pDifferences2[$j]->leftLength() > 0) {
                    $this->output->addRemovedPart($leftComparator2->substring(
                        $pDifferences2[$j]->leftStart(),
                        $pDifferences2[$j]->leftEnd()));
                }

                if ($pDifferences2[$j]->rightLength() > 0) {
                    $this->output->addAddedPart($rightComparator2->substring(
                        $pDifferences2[$j]->rightStart(),
                        $pDifferences2[$j]->rightEnd()));
                }

                $rightAtom2 = $pDifferences2[$j]->rightEnd();
            }

            if ($rightAtom2 < $rightComparator2->getRangeCount()) {
                $this->output->addClearPart($rightComparator2->substring($rightAtom2));
            }
        }
    }

    /**
     * @param  RangeDifference[]     $differences
     * @param  AtomSplitterInterface $leftComparator
     * @return RangeDifference[]
     * @throws Exception
     */
    private function preProcess(array $differences, AtomSplitterInterface $leftComparator): array
    {
        $newRanges = [];

        for ($i = 0; $i < count($differences); $i++) {
            $leftStart  = $differences[$i]->leftStart();
            $leftEnd    = $differences[$i]->leftEnd();
            $rightStart = $differences[$i]->rightStart();
            $rightEnd   = $differences[$i]->rightEnd();
            $kind       = $differences[$i]->kind();
            $temp       = $leftEnd;
            $connecting = true;

            while ($connecting && $i + 1 < count($differences) && $differences[$i + 1]->kind() == $kind) {
                $bridgeLength = 0;
                $numTokens    = max($leftEnd - $leftStart, $rightEnd - $rightStart);

                if ($numTokens > 5) {
                    if ($numTokens > 10) {
                        $bridgeLength = 3;
                    } else {
                        $bridgeLength = 2;
                    }
                }

                while ($temp < $differences[$i + 1]->leftStart() &&
                    $leftComparator->getAtom($temp instanceof DelimiterAtom) ||
                    ($bridgeLength-- > 0)) {
                    $temp++;
                }

                if ($temp == $differences[$i + 1]->leftStart())  {
                    $leftEnd  = $differences[$i + 1]->leftEnd();
                    $rightEnd = $differences[$i + 1]->rightEnd();
                    $temp     = $leftEnd;
                    $i++;
                } else {
                    $connecting = false;

                    if (!$leftComparator->getAtom($temp) instanceof DelimiterAtom) {
                        if (0 == strcmp($leftComparator->getAtom($temp)->getFullText(), '')) {
                            throw new Exception('Space found.');
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
     * @param  RangeDifference[] $differences
     * @param  int               $span
     * @return RangeDifference[]
     */
    private function preProcess2(array $differences, int $span): array
    {
        $newRanges = [];

        for ($i = 0; $i < count($differences); $i++) {
            $leftStart  = $differences[$i]->leftStart();
            $leftEnd    = $differences[$i]->leftEnd();
            $rightStart = $differences[$i]->rightStart();
            $rightEnd   = $differences[$i]->rightEnd();
            $kind       = $differences[$i]->kind();

            while ($i + 1 < count($differences) &&
                $differences[$i + 1]->kind() == $kind &&
                $differences[$i + 1]->leftStart() == $leftEnd + $span &&
                $differences[$i + 1]->rightStart() == $rightEnd + $span) {
                $leftEnd  = $differences[$i + 1]->leftEnd();
                $rightEnd = $differences[$i + 1]->rightEnd();
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
