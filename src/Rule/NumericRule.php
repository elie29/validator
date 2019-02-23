<?php

declare(strict_types = 1);

namespace Elie\Validator\Rule;

/**
 * This class verifies that a value is a valid number.
 */
class NumericRule extends AbstractRule
{

    /**#@+
     * Specific message error code
     */
    public const INVALID_NUMERIC = 'invalidNumeric';
    public const INVALID_NUMERIC_LT = 'invalidNumericLessThan';
    public const INVALID_NUMERIC_GT = 'invalidNumericGreaterThan';
    /**#@-*/

    /**#@+
     * Specific options for NumericRule
     */
    public const TRIM = 'trim';
    public const MIN = 'min';
    public const MAX = 'max';
    /**#@-*/

    /**
     * Minimun value.
     */
    protected $min = null;

    /**
     * Maximum value.
     */
    protected $max = null;

    /**
     * Params could have the following structure:
     * [
     *   'required' => {bool:optional},
     *   'trim' => {bool:optional:only if value is string},
     *   'messages' => {array:optional:key/value message patterns},
     *   'min' => {int:optional:null by default},
     *   'max' => {int:optional:null by default}
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

        $this->messages += [
            $this::INVALID_NUMERIC => '%key%: %value% is not numeric',
            $this::INVALID_NUMERIC_LT => '%key%: %value% is less than %min%',
            $this::INVALID_NUMERIC_GT => '%key%: %value% is greater than %max%',
        ];
    }

    public function validate(): int
    {
        $run = parent::validate();

        if ($run !== $this::CHECK) {
            return $run;
        }

        if (! is_numeric($this->value)) {
            return $this->setAndReturnError($this::INVALID_NUMERIC);
        }

        return $this->checkMinMax();
    }

    protected function checkMinMax(): int
    {
        if (null !== $this->min && $this->value < $this->min) {
            return $this->setAndReturnError($this::INVALID_NUMERIC_LT, [
                // in case both are needed in the message pattern
                '%min%' => $this->min,
                '%max%' => $this->max
            ]);
        }

        if (null !== $this->max && $this->value > $this->max) {
            return $this->setAndReturnError($this::INVALID_NUMERIC_GT, [
                // in case both are needed in the message pattern
                '%min%' => $this->min,
                '%max%' => $this->max
            ]);
        }

        return $this::VALID;
    }
}
