<?php
/**
 * (c) Steve Nebes <snebes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace DaisyDiff\Tag;

use DaisyDiff\Output\TextDiffOutputInterface;

/**
 * Minimal test case for Tag node.
 */
class TagTestFixture
{
    /** @var string */
    private $oldText = '';

    /** @var string */
    private $newText = '';

    /** @var TextOperation[] */
    private $results;

    public function __construct()
    {
        $this->results = new \ArrayObject();
    }

    /**
     * @param string $original
     * @param string $modified
     */
    public function performTagDiff(string $original, string $modified): void
    {
        $this->oldText = $original;
        $this->newText = $modified;

        $oldComp = new TagComparator($original);
        $newComp = new TagComparator($modified);

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
            if ($operation->getType() === OperationType::ADD_TEXT) {
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
            if ($operation->getType() === OperationType::REMOVE_TEXT) {
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
        return $this->results->getArrayCopy();
    }
}

/**
 * Type of changes as produced by the diff process.
 */
final class OperationType
{
    /** @const string */
    const NO_CHANGE   = 'nochange';
    const ADD_TEXT    = 'add';
    const REMOVE_TEXT = 'remove';
}

/**
 * Simple operation for test cases only.
 */
class TextOperation
{
    /** @var string */
    private $text = '';

    /** @var string */
    private $type = OperationType::NO_CHANGE;

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
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type): void
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
     * @param \ArrayObject $results
     */
    public function __construct(\ArrayObject $results)
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
        $operation->setType(OperationType::ADD_TEXT);

        $this->results[] = $operation;
    }

    /**
     * @param string $text
     */
    public function addRemovedPart(string $text): void
    {
        $operation = new TextOperation();
        $operation->setText($text);
        $operation->setType(OperationType::REMOVE_TEXT);

        $this->results[] = $operation;
    }

    /**
     * @param string $text
     */
    public function addClearPart(string $text): void
    {
        $operation = new TextOperation();
        $operation->setText($text);
        $operation->setType(OperationType::NO_CHANGE);

        $this->results[] = $operation;
    }
}
