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
     *   'min' => {int:optional:0 by default},
     *   'max' => {int:optional:value length by default}
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
            $this->error = "{$this->key}: {$this->value} is not numeric";
            return RuleInterface::ERROR;
        }

        return $this->checkMinMax();
    }

    protected function checkMinMax(): int
    {
        if (null !== $this->min && $this->value < $this->min) {
            $this->error = "{$this->key}: {$this->value} is less than {$this->min}";
            return RuleInterface::ERROR;
        }

        if (null !== $this->max && $this->value > $this->max) {
            $this->error = "{$this->key}: {$this->value} is greater {$this->max}";
            return RuleInterface::ERROR;
        }

        return RuleInterface::VALID;
    }
}
