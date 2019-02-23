<?php

declare(strict_types = 1);

namespace Elie\Validator\Rule;

/**
 * This class verifies that a value is a valid array.
 */
class ArrayRule extends AbstractRule
{

    /**#@+
     * Specific message error code
     */
    public const INVALID_ARRAY = 'invalidArray';
    public const INVALID_ARRAY_LENGTH = 'invalidArrayLength';
    /**#@-*/

    /**#@+
     * Specific options for ArrayRule
     */
    public const MIN = 'min';
    public const MAX = 'max';
    /**#@-*/

    /**
     * Minimun size.
     */
    protected $min = 0;

    /**
     * Maximum size.
     */
    protected $max;

    /**
     * Params could have the following structure:
     * [
     *   'required' => {bool:optional},
     *   'messages' => {array:optional:key/value message patterns},
     *   'min' => {int:optional:0 by default},
     *   'max' => {int:optional:value count by default}
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

        // + won't replace existent keys set by users
        $this->messages = $this->messages + [
            $this::INVALID_ARRAY => '%key% does not have an array value: %value%',
            $this::INVALID_ARRAY_LENGTH => '%key%: The length of %value% is not between %min% and %max%',
        ];
    }

    public function getValue()
    {
        if (! $this->value) {
            return [];
        }
        return $this->value;
    }

    public function validate(): int
    {
        $run = parent::validate();

        if ($run !== $this::CHECK) {
            return $run;
        }

        if (! is_array($this->value)) {
            return $this->setAndReturnError($this::INVALID_ARRAY);
        }

        return $this->checkMinMax();
    }

    protected function checkMinMax(): int
    {
        $len = count($this->value);
        $maxOrLen = $this->max ?: $len;

        if ($len < $this->min || $len > $maxOrLen) {
            return $this->setAndReturnError($this::INVALID_ARRAY_LENGTH, [
                '%min%' => $this->min,
                '%max%' => $this->max,
            ]);
        }

        return $this::VALID;
    }
}
