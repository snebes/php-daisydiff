<?php

declare(strict_types=1);

namespace DaisyDiff\Html;

use DaisyDiff\Html\Modification\ModificationType;
use DaisyDiff\Output\DiffOutputInterface;
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
     * @param  TextNodeComparator $ancestorComparator
     * @param  TextNodeComparator $leftComparator
     * @param  TextNodeComparator $rightComparator
     *
     * @throws
     */
    public function diff3(
        TextNodeComparator $ancestorComparator,
        TextNodeComparator $leftComparator,
        TextNodeComparator $rightComparator
    ): void {
        /** @var RangeDifference[] $differences */
        $differences = RangeDifferencer::findDifferences3($ancestorComparator, $leftComparator, $rightComparator);
        $pDifferences = $this->preProcess($differences);

        $currentIndexAncestor = 0;
        $currentIndexLeft     = 0;
        $currentIndexRight    = 0;

        foreach ($pDifferences as $d) {
            $tempKind = $d->kind();

            if (RangeDifference::ANCESTOR == $tempKind) {
                // Ignore, we won't show pseudo-conflicts currently (left and right have the same change).
                continue;
            }

            if ($d->leftStart() > $currentIndexLeft) {
                $ancestorComparator->handlePossibleChangedPart(
                    $currentIndexLeft, $d->leftStart(),
                    $currentIndexAncestor, $d->ancestorStart(),
                    $leftComparator);
            }

            if ($d->rightStart() > $currentIndexRight) {
                $ancestorComparator->handlePossibleChangedPart(
                    $currentIndexRight, $d->rightStart(),
                    $currentIndexAncestor, $d->ancestorStart(),
                    $rightComparator);
            }

            if (RangeDifference::CONFLICT == $tempKind || RangeDifference::LEFT == $tempKind) {
                // Conflicts and changes on the left side.
                if ($d->leftLength() > 0) {
                    $ancestorComparator->markAsDeleted(
                        $d->leftStart(), $d->leftEnd(), $leftComparator,
                        $d->ancestorStart(), $d->ancestorEnd(), ModificationType::ADDED);
                }
            }

            if (RangeDifference::CONFLICT == $tempKind || RangeDifference::RIGHT == $tempKind) {
                // Conflicts and changes on the right side.
                if ($d->rightLength() > 0) {
                    $ancestorComparator->markAsDeleted(
                        $d->rightStart(), $d->rightEnd(), $rightComparator,
                        $d->ancestorStart(), $d->ancestorEnd(), ModificationType::ADDED);
                }
            }

            $ancestorComparator->markAsNew($d->ancestorStart(), $d->ancestorEnd(), ModificationType::REMOVED);

            $currentIndexAncestor = $d->ancestorEnd();
            $currentIndexLeft     = $d->leftEnd();
            $currentIndexRight    = $d->rightEnd();
        }

        if ($currentIndexLeft < $leftComparator->getRangeCount()) {
            $ancestorComparator->handlePossibleChangedPart(
                $currentIndexLeft, $leftComparator->getRangeCount(),
                $currentIndexAncestor, $ancestorComparator->getRangeCount(),
                $leftComparator);
        }

        if ($currentIndexRight < $rightComparator->getRangeCount()) {
            $ancestorComparator->handlePossibleChangedPart(
                $currentIndexRight, $rightComparator->getRangeCount(),
                $currentIndexAncestor, $ancestorComparator->getRangeCount(),
                $rightComparator);
        }

        $ancestorComparator->expandWhiteSpace();
        $this->output->generateOutput($ancestorComparator->getBodyNode());
    }

    /**
     * Compares two Node Trees.
     *
     * @param  TextNodeComparator $leftComparator
     * @param  TextNodeComparator $rightComparator
     *
     * @throws
     */
    public function diff(TextNodeComparator $leftComparator, TextNodeComparator $rightComparator): void
    {
        /** @var RangeDifference[] $differences */
        $differences = RangeDifferencer::findDifferences($leftComparator, $rightComparator);

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

        for ($i = 0, $iMax = count($differences); $i < $iMax; $i++) {
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

            while  ($i + 1 < $iMax
                    && $differences[$i + 1]->kind() == $kind
                    && $this->score($leftLength, $differences[$i + 1]->leftLength(),
                                    $rightLength, $differences[$i + 1]->rightLength()) > ($differences[$i + 1]->leftStart() - $leftEnd)) {
                $ancestorEnd = $differences[$i + 1]->ancestorEnd();
                $leftEnd     = $differences[$i + 1]->leftEnd();
                $rightEnd    = $differences[$i + 1]->rightEnd();

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
     * @param int[] $numbers
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
