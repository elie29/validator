<?php

declare(strict_types = 1);

namespace Elie\Validator\Rule;

/**
 * This class verifies that a value is a time valid format "hh:mm:ss" or "hh:mm".
 * We add 0 to the left of the hour so 2:49 => 02:49 and 0 to the right of the
 * minutes ans seconds so 3:4 => 03:40:00
 */
class TimeRule extends AbstractRule
{

    public const TIME_REGEX = '/^([0-1]{1}[0-9]{1}|[2]{1}[0-3]{1}):[0-5]{1}[0-9]{1}:[0-5]{1}[0-9]{1}$/';

    public function validate(): int
    {
        $run = parent::validate();

        if ($run !== RuleInterface::CHECK) {
            return $run;
        }

        if (! static::checkTime($this->value)) {
            $this->error = "{$this->key}: {$this->value} is not a valid time";
            return RuleInterface::ERROR;
        }

        return RuleInterface::VALID;
    }

    /**
     * Check if a given time has the following format:
     * hh:mm:ss or hh:mm
     *
     * @param string $time Time to be checked.
     *
     * @return bool
     */
    public static function checkTime($time): bool
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
              . str_pad($tokens[1], 2, '0', STR_PAD_RIGHT) . ':'
              . str_pad($second, 2, '0', STR_PAD_RIGHT);

        // we test hh:mm:ss from 00:00:00 => 23:59:59
        return !! preg_match(self::TIME_REGEX, $time);
    }
}
