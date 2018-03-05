<?php declare(strict_types=1);

namespace DaisyDiff\Tag;

use InvalidArgumentException;

/**
 * An atom that represents a closing or opening tag.
 */
class TagAtom implements AtomInterface
{
    /** @var string */
    private $identifier = '';

    /** @var string */
    private $internalIdentifiers = '';

    /**
     * @param  string $s
     * @throws InvalidArgumentException
     */
    public function __construct(string $s)
    {
        if (!$this->isValidAtom($s)) {
            throw new InvalidArgumentException('The given string is not a valid tag.');
        }

        // Remove the < and >.
        $s = mb_substr($s, 1, -1);

        if (false !== ($pos = mb_strpos($s, ' '))) {
            $this->identifier          = mb_substr($s, 0, $pos);
            $this->internalIdentifiers = mb_substr($s, $pos + 1);
        } else {
            $this->identifier = $s;
        }
    }

    /** {@inheritdoc} */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /** {@inheritdoc} */
    public function getInternalIdentifiers(): string
    {
        return $this->internalIdentifiers;
    }

    /** {@inheritdoc} */
    public function hasInternalIdentifiers(): bool
    {
        return mb_strlen($this->internalIdentifiers) > 0;
    }

    /**
     * @param  string $s
     * @return bool
     */
    public static function isValidTag(string $s): bool
    {
        return
            0 == mb_strrpos($s, '<') &&
            mb_strpos($s, '>') == mb_strlen($s) - 1 &&
            mb_strlen($s) >= 3;
    }

    /** {@inheritdoc} */
    public function getFullText(): string
    {
        $s = sprintf('<%s', $this->identifier);

        if ($this->hasInternalIdentifiers()) {
            $s .= sprintf(' %s', $this->internalIdentifiers);
        }

        $s .= '>';

        return $s;
    }

    /** {@inheritdoc} */
    public function isValidAtom(string $s): bool
    {
        return self::isValidTag($s);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return sprintf('TagAtom: %s', $this->getFullText());
    }

    /** {@inheritdoc} */
    public function equalsIdentifier(AtomInterface $other): bool
    {
        return 0 == strcmp($other->getIdentifier(), $this->getIdentifier());
    }
}
