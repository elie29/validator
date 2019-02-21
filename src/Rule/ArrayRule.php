<?php

declare(strict_types = 1);

namespace Elie\Validator\Rule;

/**
 * This class verifies that a value is a valid array.
 */
class ArrayRule extends AbstractRule
{

    /**
     * Minimun value.
     */
    protected $min = 0;

    /**
     * Maximum value.
     */
    protected $max = 0;

    /**
     * Params could have the following structure:
     * [
     *   'required' => {bool:optional},
     *   'trim' => {bool:optional},
     *   'messages' => {array:optional:key/value message patterns},
     *   'min' => {int:optional:0 by default},
     *   'max' => {int:optional:value count by default}
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

        if (! is_array($this->value)) {
            return $this->setAndReturnError(self::INVALID_ARRAY);
        }

        return $this->checkMinMax();
    }

    protected function checkMinMax(): int
    {
        $len = count($this->value);
        $maxOrLen = $this->max ?: $len;

        if ($len < $this->min || $len > $maxOrLen) {
            return $this->setAndReturnError(self::INVALID_ARRAY_LENGTH, [
                '%min%' => $this->min,
                '%max%' => $this->max,
            ]);
        }

        return RuleInterface::VALID;
    }
}
