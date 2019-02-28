<?php

declare(strict_types = 1);

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
        $non_displayables[] = '/%0[0-9bcef]/i'; // url encoded 00-09, 11, 12, 14, 15
        $non_displayables[] = '/%1[0-9a-f]/i'; // url encoded 16-31
        $non_displayables[] = '/%7f/i'; // url encoded 127

        // No need for i: insensitive
        $non_displayables[] = '/[\x00-\x09\x0B\x0C\x0E-\x1F\x7F]+/S'; // 00-09, 11, 12, 14-31, 127

        do {
            $value = preg_replace($non_displayables, '', $value, -1, $count);
        } while ($count);

        return $value;
    }
}
