<?php
/**
 * (c) Steve Nebes <snebes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace SN\DaisyDiff\Tag;

/**
 * An Atom that represents a piece of ordinary text.
 */
class TextAtom implements AtomInterface
{
    /** @var string */
    private $s;

    /**
     * @param string $s
     *
     * @throws \RuntimeException
     */
    public function __construct(string $s)
    {
        if (!$this->isValidAtom($s)) {
            throw new \RuntimeException('The given String is not a valid Text Atom.');
        }

        $this->s = $s;
    }

    /** {@inheritdoc} */
    public function getFullText(): string
    {
        return $this->s;
    }

    /** {@inheritdoc} */
    public function getIdentifier(): string
    {
        return $this->s;
    }

    /** {@inheritdoc} */
    public function getInternalIdentifiers(): string
    {
        throw new \RuntimeException('This Atom has no internal identifiers.');
    }

    /** {@inheritdoc} */
    public function hasInternalIdentifiers(): bool
    {
        return false;
    }

    /** {@inheritdoc} */
    public function isValidAtom(string $s): bool
    {
        return !empty($s);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return \sprintf('TextAtom: %s', $this->getFullText());
    }

    /** {@inheritdoc} */
    public function equalsIdentifier(AtomInterface $other): bool
    {
        return $other->getIdentifier() === $this->getIdentifier();
    }
}
