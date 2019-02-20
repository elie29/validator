<?php

declare(strict_types = 1);

namespace Elie\Validator\Rule;

/**
 * This class verifies that a value is a valid string.
 */
class StringRule extends AbstractRule
{

    /**
     * Minimun string length.
     */
    protected $min = 0;

    /**
     * Maximum string length.
     */
    protected $max = 0;

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

        if (! is_string($this->value)) {
            $this->error = "{$this->key} does not have a string value";
            return RuleInterface::ERROR;
        }

        return $this->checkMinMax();
    }

    protected function checkMinMax(): int
    {
        $len = strlen($this->value);
        $maxOrLen = $this->max ?: $len;

        if ($len < $this->min || $len > $maxOrLen) {
            $this->error = "{$this->key}: The length of {$this->value} is not between {$this->min} and {$this->max}";
            return RuleInterface::ERROR;
        }

        return RuleInterface::VALID;
    }
}
