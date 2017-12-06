<?php declare(strict_types=1);

namespace DaisyDiff\Tag;

/**
 * A TextAtom with an identifier from a limited set of delimiter strings.
 */
class DelimiterAtom extends TextAtom
{
    /**
     * @param  string $c
     */
    public function __construct(?string $c)
    {
        parent::__construct('' . $c);
    }

    /**
     * @param  string $s
     * @return bool
     */
    public static function isValidDelimiter(?string $s): bool
    {
        if (empty($s) || mb_strlen($s) > 1) {
            return false;
        }

        switch ($s) {
            case '/':
            case '.':
            case '!':
            case ',':
            case ';':
            case '?':
            case ' ':
            case '=':
            case '\'':
            case '"':
            case '\t':
            case '\r':
            case '\n':
            case '[':
            case ']':
            case '{':
            case '}':
            case '(':
            case ')':
            case '&':
            case '|':
            case "\\":
            case '-':
            case '_':
            case '+':
            case '*':
            case ':':
                return true;
            default:
                return false;
        }
    }

    /** {@inheritdoc} */
    public function isValidAtom(?string $s): bool
    {
        return parent::isValidAtom($s) && self::isValidDelimiterAtom($s);
    }

    /**
     * @param  string $s
     * @return bool
     */
    private function isValidDelimiterAtom(string $s): bool
    {
        return self::isValidDelimiter($s);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        $search  = ["\n", "\r", "\t"];
        $replace = ["\\\\n", "\\\\r", "\\\\t"];

        return sprintf('DelimiterAtom: %s', str_replace($search, $replace, $this->getFullText()));
    }

    /** {@inheritdoc} */
    public function equalsIdentifier(AtomInterface $other): bool
    {
        return
            parent::equalsIdentifier($other) ||
            ($other->getIdentifier() == '' || $other->getIdentifier() == "\n") &&
            ($this->getIdentifier() == '' || $this->getIdentifier() == "\n");
    }
}
