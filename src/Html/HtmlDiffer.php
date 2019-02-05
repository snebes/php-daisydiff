<?php
/**
 * (c) Steve Nebes <snebes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace SN\DaisyDiff\Html;

use SN\DaisyDiff\Html\Modification\ModificationType;
use SN\DaisyDiff\Output\DiffOutputInterface;
use SN\RangeDifferencer\Core\LCSSettings;
use SN\RangeDifferencer\RangeDifference;
use SN\RangeDifferencer\RangeDifferencer;

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
     * @param TextNodeComparator $leftComparator  Root of the first tree.
     * @param TextNodeComparator $rightComparator Root of the second tree.
     */
    public function diff(TextNodeComparator $leftComparator, TextNodeComparator $rightComparator): void
    {
        $settings = new LCSSettings();
        $settings->setUseGreedyMethod(false);

        /** @var RangeDifference[] $differences */
        $differences = RangeDifferencer::findDifferences($leftComparator, $rightComparator, $settings);
        $pDifferences = $this->preProcess($differences);

        $currentIndexLeft = 0;
        $currentIndexRight = 0;

        /** @var RangeDifference $d */
        foreach ($pDifferences as $d) {
            if ($d->getLeftStart() > $currentIndexLeft) {
                $rightComparator->handlePossibleChangedPart(
                    $currentIndexLeft, $d->getLeftStart(),
                    $currentIndexRight, $d->getRightStart(),
                    $leftComparator);
            }

            if ($d->getLeftLength() > 0) {
                $rightComparator->markAsDeleted(
                    $d->getLeftStart(), $d->getLeftEnd(),
                    $leftComparator,
                    $d->getRightStart());
            }

            $rightComparator->markAsNew($d->getRightStart(), $d->getRightEnd());

            $currentIndexLeft = $d->getLeftEnd();
            $currentIndexRight = $d->getRightEnd();
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
     * @param TextNodeComparator $ancestorComparator
     * @param TextNodeComparator $leftComparator
     * @param TextNodeComparator $rightComparator
     */
    public function diff3(
        TextNodeComparator $ancestorComparator,
        TextNodeComparator $leftComparator,
        TextNodeComparator $rightComparator
    ): void {
        $settings = new LCSSettings();
        $settings->setUseGreedyMethod(false);

        /** @var RangeDifference[] $differences */
        $differences = RangeDifferencer::findDifferences3($ancestorComparator, $leftComparator, $rightComparator);
        $pDifferences = $this->preProcess($differences);

        $currentIndexAncestor = 0;
        $currentIndexLeft = 0;
        $currentIndexRight = 0;

        /** @var RangeDifference $d */
        foreach ($pDifferences as $d) {
            $tempKind = $d->getKind();

            if (RangeDifference::ANCESTOR === $tempKind) {
                // Ignore, we won't show pseudo-conflicts currently (left and right have the same change).
                continue;
            }

            if ($d->getLeftStart() > $currentIndexLeft) {
                $ancestorComparator->handlePossibleChangedPart(
                    $currentIndexLeft, $d->getLeftStart(),
                    $currentIndexAncestor, $d->getAncestorStart(),
                    $leftComparator);
            }

            if ($d->getRightStart() > $currentIndexRight) {
                $ancestorComparator->handlePossibleChangedPart(
                    $currentIndexRight, $d->getRightStart(),
                    $currentIndexAncestor, $d->getAncestorStart(),
                    $rightComparator);
            }

            if (RangeDifference::CONFLICT === $tempKind || RangeDifference::LEFT === $tempKind) {
                // Conflicts and changes on the left side.
                if ($d->getLeftLength() > 0) {
                    $ancestorComparator->markAsDeleted(
                        $d->getLeftStart(), $d->getLeftEnd(), $leftComparator,
                        $d->getAncestorStart(), ModificationType::ADDED);
                }
            }

            if (RangeDifference::CONFLICT === $tempKind || RangeDifference::RIGHT === $tempKind) {
                // Conflicts and changes on the right side.
                if ($d->getRightLength() > 0) {
                    $ancestorComparator->markAsDeleted(
                        $d->getRightStart(), $d->getRightEnd(), $rightComparator,
                        $d->getAncestorStart(), ModificationType::ADDED);
                }
            }

            $ancestorComparator->markAsNew($d->getAncestorStart(), $d->getAncestorEnd(), ModificationType::REMOVED);

            $currentIndexAncestor = $d->getAncestorEnd();
            $currentIndexLeft = $d->getLeftEnd();
            $currentIndexRight = $d->getRightEnd();
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
     * @param RangeDifference[] $differences
     * @return RangeDifference[]
     */
    private function preProcess(array $differences): array
    {
        /** @var RangeDifference[] */
        $newRanges = [];

        for ($i = 0, $iMax = \count($differences); $i < $iMax; $i++) {
            $ancestorStart = $differences[$i]->getAncestorStart();
            $ancestorEnd = $differences[$i]->getAncestorEnd();
            $leftStart = $differences[$i]->getLeftStart();
            $leftEnd = $differences[$i]->getLeftEnd();
            $rightStart = $differences[$i]->getRightStart();
            $rightEnd = $differences[$i]->getRightEnd();
            $kind = $differences[$i]->getKind();

            $ancestorLength = $ancestorEnd - $ancestorStart;
            $leftLength = $leftEnd - $leftStart;
            $rightLength = $rightEnd - $rightStart;

            while (
                $i + 1 < $iMax &&
                $differences[$i + 1]->getKind() === $kind &&
                $this->score($leftLength, $differences[$i + 1]->getLeftLength(), $rightLength,
                    $differences[$i + 1]->getRightLength()) > ($differences[$i + 1]->getLeftStart() - $leftEnd)
            ) {
                $ancestorEnd = $differences[$i + 1]->getAncestorEnd();
                $leftEnd = $differences[$i + 1]->getLeftEnd();
                $rightEnd = $differences[$i + 1]->getRightEnd();

                $ancestorLength = $ancestorEnd - $ancestorStart;
                $leftLength = $leftEnd - $leftStart;
                $rightLength = $rightEnd - $rightStart;

                $i++;
            }

            $newRanges[] = new RangeDifference(
                $kind,
                $rightStart, $rightLength,
                $leftStart, $leftLength,
                $ancestorStart, $ancestorLength);
        }

        return $newRanges;
    }

    /**
     * @param int[] $numbers
     * @return float
     *
     * @throws \OutOfRangeException
     */
    public static function score(int ...$numbers): float
    {
        if (\count($numbers) < 3) {
            throw new \OutOfRangeException();
        }

        if (($numbers[0] === 0 && $numbers[1] === 0) || ($numbers[2] === 0 && $numbers[3] === 0)) {
            return (float) 0;
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

        return (float) ($d / (1.5 * \count($numbers)));
    }
}
