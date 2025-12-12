<?php

declare(strict_types=1);

namespace Elie\Validator\Rule;

/**
 * This class verifies that a value exists in an array.
 */
class RangeRule extends AbstractRule
{

    /**
     * Specific message error code
     */
    public const INVALID_RANGE = 'invalidRange';

    /**#@+
     * Specific options for RangeRule
     */
    public const TRIM = 'trim';
    public const RANGE = 'range';
    /**#@-*/

    /**
     * Range values.
     * Default sets to an empty array.
     */
    protected array $range = [];

    /**
     * Params could have the following structure:
     * <code>
     * [
     *   'required' => {bool:optional:false by default},
     *   'trim' => {bool:optional:true by default:only if value is string},
     *   'messages' => {array:optional:key/value message patterns},
     *   'range' => {array:optional:empty array by default}
     * ]
     * </code>
     */
    public function __construct(int|string $key, mixed $value, array $params = [])
    {
        parent::__construct($key, $value, $params);

        if (isset($params[self::RANGE])) {
            $this->range = $params[self::RANGE];
        }

        $this->messages += [
            self::INVALID_RANGE => '%key%: %value% is out of range %range%',
        ];
    }

    public function validate(): int
    {
        $run = parent::validate();

        if ($run !== $this::CHECK) {
            return $run;
        }

        if (!in_array($this->value, $this->range, true)) {
            return $this->setAndReturnError(self::INVALID_RANGE, [
                '%range%' => $this->stringify($this->range),
            ]);
        }

        return $this::VALID;
    }
}
