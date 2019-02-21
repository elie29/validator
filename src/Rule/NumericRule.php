<?php

declare(strict_types = 1);

namespace Elie\Validator\Rule;

/**
 * This class verifies that a value is a valid number.
 */
class NumericRule extends AbstractRule
{

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
     *   'trim' => {bool:optional},
     *   'messages' => {array:optional:key/value message patterns},
     *   'min' => {int:optional:null by default},
     *   'max' => {int:optional:null by default}
     * ]
     */
    public function __construct(string $key, $value, array $params = [])
    {
        parent::__construct($key, $value, $params);

        if (isset($params['min'])) {
            $this->min = (int) $params['min'];
        }
        if (isset($params['max'])) {
            $this->max = (int) $params['max'];
        }
    }

    public function validate(): int
    {
        $run = parent::validate();

        if ($run !== RuleInterface::CHECK) {
            return $run;
        }

        if (! is_numeric($this->value)) {
            return $this->setAndReturnError(self::INVALID_NUMERIC);
        }

        return $this->checkMinMax();
    }

    protected function checkMinMax(): int
    {
        if (null !== $this->min && $this->value < $this->min) {
            return $this->setAndReturnError(self::INVALID_NUMERIC_LT, [
                // in case both are needed in the message pattern
                '%min%' => $this->min,
                '%max%' => $this->max
            ]);
        }

        if (null !== $this->max && $this->value > $this->max) {
            return $this->setAndReturnError(self::INVALID_NUMERIC_GT, [
                // in case both are needed in the message pattern
                '%min%' => $this->min,
                '%max%' => $this->max
            ]);
        }

        return RuleInterface::VALID;
    }
}
