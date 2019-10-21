<?php

declare(strict_types = 1);

namespace Elie\Validator\Rule;

/**
 * This class verifies that a value is a valid string.
 */
class StringRule extends AbstractRule
{

    /**#@+
     * Specific message error code
     */
    public const INVALID_STRING = 'invalidString';
    public const INVALID_STRING_LENGTH = 'invalidStringLength';
    /**#@-*/

    /**#@+
     * Specific options for StringRule
     */
    public const TRIM = 'trim';
    public const MIN = 'min';
    public const MAX = 'max';
    /**#@-*/

    /**
     * Minimum string length.
     */
    protected $min = 0;

    /**
     * Maximum string length.
     */
    protected $max = 0;

    /**
     * Params could have the following structure:
     * [
     *   'required' => {bool:optional:false by default},
     *   'trim' => {bool:optional:true by default},
     *   'messages' => {array:optional:key/value message patterns},
     *   'min' => {int:optional:0 by default},
     *   'max' => {int:optional:value length by default}
     * ]
     */
    public function __construct(string $key, $value, array $params = [])
    {
        parent::__construct($key, $value, $params);

        if (isset($params[$this::MIN])) {
            $this->min = (int) $params[$this::MIN];
        }
        if (isset($params[$this::MAX])) {
            $this->max = (int) $params[$this::MAX];
        }

        $this->messages = $this->messages + [
            $this::INVALID_STRING => '%key% does not have a string value: %value%',
            $this::INVALID_STRING_LENGTH => '%key%: The length of %value% is not between %min% and %max%',
        ];
    }

    public function validate(): int
    {
        $run = parent::validate();

        if ($run !== $this::CHECK) {
            return $run;
        }

        if (! is_string($this->value)) {
            return $this->setAndReturnError($this::INVALID_STRING);
        }

        return $this->checkMinMax();
    }

    protected function checkMinMax(): int
    {
        $len = strlen($this->value);
        $maxOrLen = $this->max ?: $len;

        if ($len < $this->min || $len > $maxOrLen) {
            return $this->setAndReturnError($this::INVALID_STRING_LENGTH, [
                '%min%' => $this->min,
                '%max%' => $this->max,
            ]);
        }

        return $this::VALID;
    }
}
