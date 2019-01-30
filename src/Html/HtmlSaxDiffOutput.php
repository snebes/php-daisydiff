<?php
/**
 * (c) Steve Nebes <snebes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace DaisyDiff\Html;

use DaisyDiff\Html\Dom\ImageNode;
use DaisyDiff\Html\Dom\TagNode;
use DaisyDiff\Html\Dom\TextNode;
use DaisyDiff\Html\Modification\Modification;
use DaisyDiff\Html\Modification\ModificationType;
use DaisyDiff\Output\DiffOutputInterface;
use DaisyDiff\Xml\ContentHandlerInterface;

/**
 * Takes a branch root and creates an HTML file for it.
 *
 * @internal
 */
class HtmlSaxDiffOutput implements DiffOutputInterface
{
    /** @var ContentHandlerInterface */
    private $handler;

    /**
     * @param ContentHandlerInterface $handler
     */
    public function __construct(ContentHandlerInterface $handler)
    {
        $this->handler = $handler;
    }

    /**
     * @param TagNode $node
     */
    public function generateOutput(TagNode $node): void
    {
        if ('img' !== $node->getQName() && 'body' !== $node->getQName()) {
            $this->handler->startElement($node->getQName(), $node->getAttributes());
        }

        $newStarted = false;
        $remStarted = false;
        $changeStarted = false;
        $conflictStarted = false;
        $changeText = '';

        foreach ($node as $child) {
            if ($child instanceof TagNode) {
                if ($newStarted) {
                    $this->handler->endElement('ins');
                    $newStarted = false;
                } elseif ($changeStarted) {
                    $this->handler->endElement('span');
                    $changeStarted = false;
                } elseif ($remStarted) {
                    $this->handler->endElement('del');
                    $remStarted = false;
                } elseif ($conflictStarted) {
                    $this->handler->endElement('span');
                    $conflictStarted = false;
                }

                $this->generateOutput($child);
            } elseif ($child instanceof TextNode) {
                $mod = $child->getModification();

                if ($newStarted && ($mod->getOutputType() !== ModificationType::ADDED || $mod->isFirstOfId())) {
                    $this->handler->endElement('ins');
                    $newStarted = false;
                } elseif (
                    $changeStarted && (
                        $mod->getOutputType() !== ModificationType::CHANGED ||
                        $mod->getChanges() !== $changeText ||
                        $mod->isFirstOfId()
                    )
                ) {
                    $this->handler->endElement('span');
                    $changeStarted = false;
                } elseif ($remStarted && ($mod->getOutputType() !== ModificationType::REMOVED || $mod->isFirstOfId())) {
                    $this->handler->endElement('del');
                    $remStarted = false;
                } elseif (
                    $conflictStarted &&
                    ($mod->getOutputType() !== ModificationType::CONFLICT || $mod->isFirstOfId())
                ) {
                    $this->handler->endElement('span');
                    $conflictStarted = false;
                }

                // No else because a removed part can just be closed and a new part can start.
                if (!$newStarted && $mod->getOutputType() === ModificationType::ADDED) {
                    $attrs = ['class' => 'diff-html-added'];
                    $this->handler->startElement('ins', $attrs);
                    $newStarted = true;
                } elseif (!$changeStarted && $mod->getOutputType() === ModificationType::CHANGED) {
                    $attrs = ['class' => 'diff-html-changed'];
                    $this->handler->startElement('span', $attrs);
                    $changeStarted = true;
                    $changeText = $mod->getChanges();
                } elseif (!$remStarted && $mod->getOutputType() === ModificationType::REMOVED) {
                    $attrs = ['class' => 'diff-html-removed'];
                    $this->handler->startElement('del', $attrs);
                    $remStarted = true;
                } elseif (!$conflictStarted && $mod->getOutputType() === ModificationType::CONFLICT) {
                    $attrs = ['class' => 'diff-html-conflict'];
                    $this->handler->startElement('span', $attrs);
                    $conflictStarted = true;
                }

                if ($child instanceof ImageNode) {
                    $this->writeImage($child);
                } else {
                    $this->handler->characters($child->getText());
                }
            }
        }

        if ($newStarted) {
            $this->handler->endElement('ins');
        } elseif ($changeStarted) {
            $this->handler->endElement('span');
        } elseif ($remStarted) {
            $this->handler->endElement('del');
        } elseif ($conflictStarted) {
            $this->handler->endElement('span');
        }

        if ('img' !== $node->getQName() && 'body' !== $node->getQName()) {
            $this->handler->endElement($node->getQName());
        }
    }

    /**
     * @param ImageNode $imageNode
     */
    private function writeImage(ImageNode $imageNode): void
    {
        $attrs = $imageNode->getAttributes();

        if ($imageNode->getModification()->getOutputType() === ModificationType::REMOVED) {
            $attrs['changeType'] = 'diff-removed-image';
        } elseif ($imageNode->getModification()->getOutputType() === ModificationType::ADDED) {
            $attrs['changeType'] = 'diff-added-image';
        } elseif ($imageNode->getModification()->getOutputType() === ModificationType::CONFLICT) {
            $attrs['changeType'] = 'diff-conflict-image';
        }

        $this->handler->startElement('img', $attrs);
        $this->handler->endElement('img');
    }
}
