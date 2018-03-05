<?php

namespace DaisyDiff\Tag;

use DaisyDiff\Output\TextDiffOutputInterface;

/**
 * Minimal test case for Tag node.
 */
class TagTestFixture
{
    /** @var TextOperation[] */
    private $results = [];

    /**
     * @param string $oldText
     * @param string $newText
     */
    public function performTagDiff(string $oldText, string $newText): void
    {
        $oldComp = new TagComparator($oldText);
        $newComp = new TagComparator($newText);

        $output = new DummyOutput($this->results);
        $differ = new TagDiffer($output);
        $differ->diff($oldComp, $newComp);
    }

    /**
     * Attempts to re-construct the original text by looking at the diff result.
     *
     * @return string
     */
    public function getReconstructedOriginalText(): string
    {
        $result = '';

        foreach ($this->results as $operation) {
            if ($operation->getType() == TextOperation::ADD_TEXT) {
                continue;
            }

            $result .= $operation->getText();
        }

        return $result;
    }

    /**
     * Attempts to re-construct the modified text by looking at the diff result.
     *
     * @return string
     */
    public function getReconstructedModifiedText(): string
    {
        $result = '';

        foreach ($this->results as $operation) {
            if ($operation->getType() == TextOperation::REMOVE_TEXT) {
                continue;
            }

            $result .= $operation->getText();
        }

        return $result;
    }

    /**
     * @return TextOperation[]
     */
    public function getResults(): array
    {
        return $this->results;
    }
}

/**
 * Simple operation for test cases only.
 */
class TextOperation
{
    /** @const int */
    const NO_CHANGE   = 0;
    const ADD_TEXT    = 1;
    const REMOVE_TEXT = -1;

    /** @var string */
    private $text = '';

    /** @var int */
    private $type = 0;

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @param string $text
     */
    public function setText(string $text): void
    {
        $this->text = $text;
    }

    /**
     * @return int
     */
    public function getType(): int
    {
        return $this->text;
    }

    /**
     * @param int $type
     */
    public function setType(int $type): void
    {
        $this->type = $type;
    }
}

/**
 * Dummy output that holds all results in a linear list.
 */
class DummyOutput implements TextDiffOutputInterface
{
    /** @var TextOperation[] */
    private $results;

    /**
     * @param array $results
     */
    public function __construct(array &$results)
    {
        $this->results = $results;
    }

    /**
     * @param string $text
     */
    public function addAddedPart(string $text): void
    {
        $operation = new TextOperation();
        $operation->setText($text);
        $operation->setType(TextOperation::ADD_TEXT);
    }

    /**
     * @param string $text
     */
    public function addRemovedPart(string $text): void
    {
        $operation = new TextOperation();
        $operation->setText($text);
        $operation->setType(TextOperation::REMOVE_TEXT);
    }

    /**
     * @param string $text
     */
    public function addClearPart(string $text): void
    {
        $operation = new TextOperation();
        $operation->setText($text);
        $operation->setType(TextOperation::NO_CHANGE);
    }
}
