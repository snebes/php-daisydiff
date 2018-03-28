<?php declare(strict_types=1);

namespace DaisyDiff\Html;

use DaisyDiff\Output\DiffOutputInterface;
use DaisyDiff\RangeDifferencer\Core\LCSSettings;
use DaisyDiff\RangeDifferencer\RangeDifference;
use DaisyDiff\RangeDifferencer\RangeDifferencer;
use OutOfBoundsException;

/**
 * Takes TextNodeComparator instances, computes the difference between them, marks the changes, and outputs a merged
 * tree to a [] instance.
 */
class HtmlDiffer
{
    /** @var DiffOutputInterface */
    private $output;

    /**
     * @param DiffOutputInterface $output
     */
    public function __construct(DiffOutputInterface $output)
    {
        $this->output = $output;
    }

    /**
     * Compares two Node Trees.
     *
     * @param  TextNodeComparator $leftComparator
     * @param  TextNodeComparator $rightComparator
     * @return void
     */
    public function diff(TextNodeComparator $leftComparator, TextNodeComparator $rightComparator): void
    {
        // Configure LCS.
        $settings = new LCSSettings();
        $settings->setUseGreedyMethod(true);

        /** @var RangeDifference[] $differences */
        $differences = RangeDifferencer::findDifferences($leftComparator, $rightComparator, $settings);

        /** @var RangeDifference[] */
        $pDifferences = $this->preProcess($differences);
        $currentIndexLeft  = 0;
        $currentIndexRight = 0;

        foreach ($pDifferences as $d) {
            if ($d->leftStart() > $currentIndexLeft) {
                $rightComparator->handlePossibleChangedPart(
                    $currentIndexLeft, $d->leftStart(),
                    $currentIndexRight, $d->rightStart(),
                    $leftComparator);
            }

            if ($d->leftLength() > 0) {
                $rightComparator->markAsDeleted(
                    $d->leftStart(), $d->leftEnd(),
                    $leftComparator,
                    $d->rightStart(), $d->rightEnd());
            }

            $rightComparator->markAsNew($d->rightStart(), $d->rightEnd());

            $currentIndexLeft  = $d->leftEnd();
            $currentIndexRight = $d->rightEnd();
        }

        if ($currentIndexLeft < $leftComparator->getRangeCount()) {
            $rightComparator->handlePossibleChangedPart(
                $currentIndexLeft, $leftComparator->getRangeCount(),
                $currentIndexRight, $rightComparator->getRangeCount(),
                $leftComparator);
        }

        $rightComparator->expandWhiteSpace();
        $this->output->generateOutput($rightComparator->getBodyNode());
    }

    /**
     * @param  RangeDifference[] $differences
     * @return RangeDifference[]
     */
    private function preProcess(array $differences): array
    {
        /** @var RangeDifference[] */
        $newRanges = [];

        for ($i = 0; $i < count($differences); $i++) {
            $ancestorStart = $differences[$i]->ancestorStart();
            $ancestorEnd   = $differences[$i]->ancestorEnd();
            $leftStart     = $differences[$i]->leftStart();
            $leftEnd       = $differences[$i]->leftEnd();
            $rightStart    = $differences[$i]->rightStart();
            $rightEnd      = $differences[$i]->rightEnd();
            $kind          = $differences[$i]->kind();

            $ancestorLength = $ancestorEnd - $ancestorStart;
            $leftLength     = $leftEnd - $leftStart;
            $rightLength    = $rightEnd - $rightStart;

            while  ($i + 1 < count($differences) &&
                    $differences[$i + 1]->kind() == $kind &&
                    $this->score($leftLength, $differences[$i + 1]->leftLength(),
                                $rightLength, $differences[$i + 1]->rightLength()) > ($differences[$i + 1]->leftStart() - $leftEnd)) {
                $ancestorEnd    = $differences[$i + 1]->ancestorEnd();
                $leftEnd        = $differences[$i + 1]->leftEnd();
                $rightEnd       = $differences[$i + 1]->rightEnd();

                $ancestorLength = $ancestorEnd - $ancestorStart;
                $leftLength     = $leftEnd - $leftStart;
                $rightLength    = $rightEnd - $rightStart;

                $i++;
            }

            $newRanges[] = new RangeDifference($kind,
                $rightStart, $rightLength,
                $leftStart, $leftLength,
                $ancestorStart, $ancestorLength);
        }

        return $newRanges;
    }

    /**
     * @param  int[] ...$numbers
     * @return float
     */
    public static function score(int ...$numbers): float
    {
        if (count($numbers) < 3) {
            throw new OutOfBoundsException('Need at least 3 numbers.');
        }

        if (($numbers[0] == 0 && $numbers[1] == 0) || ($numbers[2] == 0 && $numbers[3] == 0)) {
            return floatval(0);
        }

        $d = 0;

        foreach ($numbers as $number) {
            while ($number > 3) {
                $d += 3;
                $number -= 3;
                $number *= 0.5;
            }

            $d += $number;
        }

        return floatval($d / (1.5 * count($numbers)));
    }
}
