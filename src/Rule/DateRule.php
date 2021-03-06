<?php

declare(strict_types = 1);

namespace Elie\Validator\Rule;

/**
 * This class validates a Gregorian date regarding the given
 * {@link format} and {@link separator}.
 * It uses {@link checkdate} if the given date and the format are valid.
 */
class DateRule extends AbstractRule
{

    /**#@+
     * Specific message code error
     */
    public const INVALID_DATE = 'invalidDate';
    public const INVALID_DATE_FORMAT = 'invalidDateFormat';
    /**#@-*/

    /**#@+
     * Specific options for DateRule
     */
    public const TRIM = 'trim';
    public const FORMAT = 'format';
    public const SEPARATOR = 'separator';
    /**#@-*/

    /**
     * Date format.
     * simple: d/m/y => 2/9/71
     * full: dd/mm/yyyy => 02/09/1971
     * We can provide one or more formats to validate a date.
     * format tokens are :
     *     d: day from 1 to 31
     *     dd: day from 01 to 31
     *     m: month from 1 to 12
     *     mm: month from 01 to 12
     *     yy: Year with two digits
     *         if year >= 70, we add 1900
     *         otherwise we add 2000
     *         e.g.:
     *             69 => 2069
     *             72 => 1972
     *     yyyy: Year with four digits, we can omit all zeros on the left
     *         2  => 0002
     *         69 => 0069
     *         72 => 0072
     * @var array
     */
    protected $format = ['dd/mm/yyyy'];

    /**
     * Accepted separators for a date.
     * comma, dash, dot and slash.
     * @var string
     */
    protected $separator = '[,-./]';

    /**
     * Params could have the following structure:
     * [
     *   'required' => {bool:optional:false by default},
     *   'trim' => {bool:optional:true by default},
     *   'messages' => {array:optional:key/value message patterns},
     *   'format'=>{string|array:optional:dd/mm/yyyy by default},
     *   'separator'=>{string:optional:(,-./) by default}
     * ]
     */
    public function __construct($key, $value, array $params = [])
    {
        parent::__construct($key, $value, $params);

        if (isset($params[$this::FORMAT])) {
            $this->setFormat($params[$this::FORMAT]);
        }

        if (isset($params[$this::SEPARATOR])) {
            $this->setSeparator($params[$this::SEPARATOR]);
        }

        $this->messages = $this->messages + [
            $this::INVALID_DATE => '%key%: %value% is not a valid date',
            $this::INVALID_DATE_FORMAT => '%key%: %value% does not have a valid format: ' .
                '%format% or separator: %separator%',
        ];
    }

    public function getFormat(): array
    {
        return $this->format;
    }

    public function getSeparator(): string
    {
        return $this->separator;
    }

    /**
     * @param array|string $format
     */
    public function setFormat($format): self
    {
        $this->format = (array) $format;
        return $this;
    }

    public function setSeparator(string $separator): self
    {
        $this->separator = $separator;
        return $this;
    }

    public function validate(): int
    {
        $run = parent::validate();

        if ($run !== $this::CHECK) {
            return $run;
        }

        // We try to check value over all format
        $codeError = $this::INVALID_DATE;
        foreach ($this->getFormat() as $format) {
            $codeError = static::checkDate($this->separator, $format, $this->value);
            if ($codeError === null) {
                // The first valid format
                return $this::VALID;
            }
        }

        return $this->setAndReturnError($codeError, [
            '%format%' => $this->stringify($this->format),
            '%separator%' => $this->separator,
        ]);
    }

    /**
     * Validate a Gregorian date according to separator and format options.
     * <code>
     *    DateRule::checkDate('[/]', 'dd/mm/yyyy', '27/02/2017'); // returns null<br/>
     *    DateRule::checkDate('[/]', 'dd/mm/yyyy', '29/02/2017'); // returns INVALID_DATE<br/>
     * </code>
     *
     * @return string|NULL Null if date is valid otherwise INVALID_DATE|INVALID_DATE_FORMAT
     */
    public static function checkDate(string $separator, string $format, string $date): ?string
    {
        $separator = '#' . $separator . '#';

        $tokens = preg_split($separator, $format);

        if ($tokens === false || static::invalidTokens($tokens)) {
            return self::INVALID_DATE_FORMAT;
        }

        $dates = preg_split($separator, $date);

        // We must have 3 tokens for a date
        if ($dates === false || count($dates) !== 3) {
            return self::INVALID_DATE;
        }

        return static::checkDatesWithTokens($tokens, $dates);
    }

    protected static function invalidTokens(array $tokens): bool
    {
        $seen = [];
        foreach ($tokens as $token) {
            // token already seen
            if (isset($seen[$token])) {
                return true;
            }
            $seen[$token] = true;
        }

        // We must have 3 tokens
        return count($seen) !== 3;
    }

    protected static function checkDatesWithTokens(array $tokens, array $dates): ?string
    {
        $d = $m = $y = 0;

        // Loop over format tokens
        for ($t = 0; $t < 3; $t += 1) {
            switch ($tokens[$t]) {
                case 'd':
                case 'dd':
                    $d = (int) $dates[$t];
                    break;
                case 'm':
                case 'mm':
                    $m = (int) $dates[$t];
                    break;
                case 'yy':
                    $y = (int) $dates[$t];
                    $y = ($y >= 70) ? $y + 1900 : $y + 2000;
                    break;
                case 'yyyy':
                    $y = (int) $dates[$t];
                    break;
                default:
                    // No valid format
                    return self::INVALID_DATE_FORMAT;
            }
        }

        return checkdate($m, $d, $y) ? null : self::INVALID_DATE;
    }
}
