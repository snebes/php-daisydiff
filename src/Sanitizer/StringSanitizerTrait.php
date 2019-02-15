<?php
/**
 * (c) Steve Nebes <snebes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace SN\DaisyDiff\Sanitizer;

/**
 * @internal
 */
trait StringSanitizerTrait
{
    /** @var array<string, string> */
    private static $replacements = [
        '&quot;' => '&#34;',
        '+'      => '&#43;',
        '='      => '&#61;',
        '@'      => '&#64;',
        '`'      => '&#96;',
        '＜'      => '&#xFF1C;',
        '＞'      => '&#xFF1E;',
        '＋'      => '&#xFF0B;',
        '＝'      => '&#xFF1D;',
        '＠'      => '&#xFF20;',
        '｀'      => '&#xFF40;',
    ];

    /**
     * @param string $string
     * @return string
     */
    public function encodeHtmlEntities(string $string): string
    {
        $string = \htmlspecialchars($string, \ENT_QUOTES | \ENT_SUBSTITUTE, 'UTF-8');
        $string = \str_replace(\array_keys(self::$replacements), \array_values(self::$replacements), $string);

        return $string;
    }
}
