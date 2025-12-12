<?php

declare(strict_types=1);

namespace Elie\Validator\Helper;

class Text
{

    /**
     * Remove Invisible Characters.
     *
     * This prevents sandwiching null characters
     * between ascii characters, like Java\0script.
     *
     * @param string $value String to be cleaned.
     *
     * @return string
     */
    public static function removeInvisibleChars(string $value): string
    {
        $non_displayables = [];
        $count = 0;

        // every control except character newline (dec 10)
        // carriage return (dec 13)
        $non_displayables[] = '/%0[0-9abcdef]/i'; // url encoded 00-09, 11, 12, 14, 15
        $non_displayables[] = '/%1[0-9a-f]/i'; // url encoded 16-31
        $non_displayables[] = '/%7f/i'; // url encoded 127

        // No need for i: insensitive
        $non_displayables[] = '/[\x00-\x09\x0B\x0C\x0E-\x1F\x7F]+/S'; // 00-09, 11, 12, 14-31, 127

        do {
            $value = preg_replace($non_displayables, '', $value, -1, $count);
        } while ($count);

        // Remove overlong UTF-8 encodings of null byte FIRST (security bypass attempt)
        // These must be removed before Unicode regex processing as they can cause issues
        // C0 80 is an overlong encoding of null
        if ($value !== '') {
            $value = preg_replace('/\xC0[\x80-\xBF]/', '', $value) ?? '';
            $value = preg_replace('/\xE0[\x80-\x9F][\x80-\xBF]/', '', $value) ?? '';
            $value = preg_replace('/\xF0[\x80-\x8F][\x80-\xBF][\x80-\xBF]/', '', $value) ?? '';
        }

        // Remove dangerous Unicode control characters
        // U+200B (Zero-width space), U+200C-U+200F (various joiners/separators)
        // U+202A-U+202E (Bidirectional text controls - used in phishing)
        // U+2060-U+2064 (Word joiner, invisible operators)
        // U+FEFF (Zero-width no-break space/BOM)
        if ($value !== '') {
            $result = preg_replace('/[\x{200B}-\x{200F}\x{202A}-\x{202E}\x{2060}-\x{2064}\x{FEFF}]/u', '', $value);
            // Only use a result if not null (invalid UTF-8 can return null)
            $value = $result ?? $value;
        }

        return $value;
    }
}
