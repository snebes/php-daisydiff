<?php

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
 */
class HtmlSaxDiffOutput implements DiffOutputInterface
{
    /** @var ContentHandlerInterface */
    private $handler;

    /** @var string */
    private $prefix;

    /**
     * @param ContentHandlerInterface $handler
     * @param string                  $prefix
     */
    public function __construct(ContentHandlerInterface $handler, string $prefix)
    {
        $this->handler = $handler;
        $this->prefix  = $prefix;
    }

    /**
     * @param TagNode $node
     */
    public function generateOutput(TagNode $node): void
    {
        if (0 !== strcasecmp($node->getQName(), 'img') && 0 !== strcasecmp($node->getQName(), 'body')) {
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
                } else if ($changeStarted) {
                    $this->handler->endElement('span');
                    $changeStarted = false;
                } else if ($remStarted) {
                    $this->handler->endElement('del');
                    $remStarted = false;
                } else if ($conflictStarted) {
                    $this->handler->endElement('span');
                    $conflictStarted = false;
                }

                $this->generateOutput($child);
            } else if ($child instanceof TextNode) {
                $mod = $child->getModification();

                if ($newStarted && ($mod->getOutputType() !== ModificationType::ADDED || $mod->isFirstOfId())) {
                    $this->handler->endElement('ins');
                    $newStarted = false;
                } else if ($changeStarted
                    && (
                        $mod->getOutputType() !== ModificationType::CHANGED
                        || $mod->getChanges() !== $changeText
                        || $mod->isFirstOfId())) {
                    $this->handler->endElement('span');
                    $changeStarted = false;
                } else if ($remStarted && ($mod->getOutputType() !== ModificationType::REMOVED || $mod->isFirstOfId())) {
                    $this->handler->endElement('del');
                    $remStarted = false;
                } else if ($conflictStarted &&
                    ($mod->getOutputType() !== ModificationType::CONFLICT || $mod->isFirstOfId())) {
                    $this->handler->endElement('span');
                    $conflictStarted = false;
                }

                // No else because a removed part can just be closed and a new part can start.
                if (!$newStarted && $mod->getOutputType() === ModificationType::ADDED) {
                    $attrs = ['class' => 'diff-html-added'];

                    if ($mod->isFirstOfId()) {
                        $attrs['id'] = sprintf('%s-%s-%s', $mod->getOutputType(), $this->prefix, $mod->getId());
                    }

                    $this->addAttributes($mod, $attrs);
                    $this->handler->startElement('ins', $attrs);

                    $newStarted = true;
                } else if (!$changeStarted && $mod->getOutputType() == ModificationType::CHANGED) {
                    $attrs = ['class' => 'diff-html-changed'];

                    if ($mod->isFirstOfId()) {
                        $attrs['id'] = sprintf('%s-%s-%s', $mod->getOutputType(), $this->prefix, $mod->getId());
                    }

                    $this->addAttributes($mod, $attrs);
                    $this->handler->startElement('span', $attrs);

                    $changeStarted = true;
                    $changeText    = $mod->getChanges();
                } else if (!$remStarted && $mod->getOutputType() == ModificationType::REMOVED) {
                    $attrs = ['class' => 'diff-html-removed'];

                    if ($mod->isFirstOfId()) {
                        $attrs['id'] = sprintf('%s-%s-%s', $mod->getOutputType(), $this->prefix, $mod->getId());
                    }

                    $this->addAttributes($mod, $attrs);
                    $this->handler->startElement('del', $attrs);

                    $remStarted = true;
                } else if (!$conflictStarted && $mod->getOutputType() == ModificationType::CONFLICT) {
                    $attrs = ['class' => 'diff-html-conflict'];

                    if ($mod->isFirstOfId()) {
                        $attrs['id'] = sprintf('%s-%s-%s', $mod->getOutputType(), $this->prefix, $mod->getId());
                    }

                    $this->addAttributes($mod, $attrs);
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
        } else if ($changeStarted) {
            $this->handler->endElement('span');
        } else if ($remStarted) {
            $this->handler->endElement('del');
        } else if ($conflictStarted) {
            $this->handler->endElement('span');
        }

        if (0 !== strcasecmp($node->getQName(), 'img') && 0 !== strcasecmp($node->getQName(), 'body')) {
            $this->handler->endElement($node->getQName());
        }
    }

    /**
     * @param ImageNode $imageNode
     */
    private function writeImage(ImageNode $imageNode): void
    {
        $attrs = $imageNode->getAttributes();

        if ($imageNode->getModification()->getOutputType() == ModificationType::REMOVED) {
            $attrs['changeType'] = 'diff-removed-image';
        } else if ($imageNode->getModification()->getOutputType() == ModificationType::ADDED) {
            $attrs['changeType'] = 'diff-added-image';
        } else if ($imageNode->getModification()->getOutputType() == ModificationType::CONFLICT) {
            $attrs['changeType'] = 'diff-conflict-image';
        }

        $this->handler->startElement('img', $attrs);
        $this->handler->endElement('img');
    }

    /**
     * @param Modification $mod
     * @param array        $attrs
     */
    private function addAttributes(Modification $mod, array &$attrs): void
    {
//        if ($mod->getOutputType() == ModificationType::CHANGED) {
//            $changes = $mod->getChanges();
//            $attrs['changes'] = htmlspecialchars($changes);
//        }
//
//        // Add previous changes.
//        if (null === $mod->getPrevious()) {
//            $attrs['previous'] = sprintf('first-%s', $this->prefix);
//        } else {
//            $attrs['previous'] = sprintf('%s-%s-%s',
//                $mod->getPrevious()->getOutputType(),
//                $this->prefix,
//                $mod->getPrevious()->getId());
//        }
//
//        // Add changeId.
//        $attrs['changeId'] = sprintf('%s-%s-%s', $mod->getOutputType(), $this->prefix, $mod->getId());
//
//        // Add next changes.
//        if (null === $mod->getNext()) {
//            $attrs['next'] = sprintf('last-%s', $this->prefix);
//        } else {
//            $attrs['next'] = sprintf('%s-%s-%s',
//                $mod->getNext()->getOutputType(),
//                $this->prefix,
//                $mod->getNext()->getId());
//        }
    }
}
