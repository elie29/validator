<?php

declare(strict_types=1);

namespace Elie\Validator\Rule;

/**
 * This class verifies that a value is a time-valid format "hh:mm:ss" or "hh:mm".
 * We add 0 to the left of the hour so 2:49 => 02:49 and 0 to the right of the
 * minutes and seconds so 3:4 => 03:40:00
 */
class TimeRule extends AbstractRule
{

    /**
     * Specific message error code
     */
    public const INVALID_TIME = 'invalidTime';

    /**
     * Specific options for TimeRule
     */
    public const TRIM = 'trim';

    protected const TIME_REGEX = '/^([0-1][0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$/';

    /**
     * Params could have the following structure:
     * <code>
     * [
     *   'required' => {bool:optional:false by default},
     *   'trim' => {bool:optional:true by default},
     *   'messages' => {array:optional:key/value message patterns}
     * ]
     * </code>
     */
    public function __construct(int|string $key, mixed $value, array $params = [])
    {
        parent::__construct($key, $value, $params);

        $this->messages += [
            $this::INVALID_TIME => '%key%: %value% is not a valid time',
        ];
    }

    /**
     * Check if a given time has the following format:
     * hh:mm:ss or hh:mm
     *
     * @param string $time Time to be checked.
     *
     * @return bool
     */
    public static function checkTime(string $time): bool
    {
        $tokens = explode(':', $time);

        $count = count($tokens);
        if ($count < 2 || $count > 3) {
            // time must be hh:mm or hh:mm:ss
            return false;
        }

        // seconds are optional
        $second = ($count == 3) ? $tokens[2] : '0';

        // We put time in hh:mm:ss format with padding
        $time = str_pad($tokens[0], 2, '0', STR_PAD_LEFT) . ':'
            . str_pad($tokens[1], 2, '0') . ':'
            . str_pad($second, 2, '0');

        // we test hh:mm:ss from 00:00:00 => 23:59:59
        return (bool)preg_match(self::TIME_REGEX, $time);
    }

    public function validate(): int
    {
        $run = parent::validate();

        if ($run !== $this::CHECK) {
            return $run;
        }

        if (!static::checkTime($this->value)) {
            return $this->setAndReturnError($this::INVALID_TIME);
        }

        return $this::VALID;
    }
}
