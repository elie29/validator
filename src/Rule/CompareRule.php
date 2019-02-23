<?php

declare(strict_types = 1);

namespace Elie\Validator\Rule;

/**
 * This class verifies that a value is valid.
 */
class CompareRule extends AbstractRule implements CompareConstants
{

    /**
     * Sign value supported ==, ===, !=, !==, <=, >=, <, >.
     * Default sets to ==.
     * @var string
     */
    protected $sign = self::EQ;

    /**
     * Expected value.
     * Default sets to null.
     * @var mixed
     */
    protected $expected = null;

    /**
     * Params could have the following structure:
     * [
     *   'required' => {bool:optional:false by default},
     *   'trim' => {bool:optional:true by default},
     *   'messages' => {array:optional:key/value message patterns},
     *   'sign' => {string:optional:EQ by default},
     *   'expected' => {mixed:optional:null by default}
     * ]
     */
    public function __construct(string $key, $value, array $params = [])
    {
        parent::__construct($key, $value, $params);

        if (isset($params[$this::SIGN])) {
            $this->sign = $params[$this::SIGN];
        }

        if (isset($params['expected'])) {
            $this->expected = $params['expected'];
        }

        $this->messages = $this->messages + [
            $this::INVALID_COMPARE => '%key%: %value% is not %label% %expected%',
        ];
    }

    public function validate(): int
    {
        $run = parent::validate();

        if ($run !== $this::CHECK) {
            return $run;
        }

        $method = $this->sign;
        if (! $this->$method()) {
            return $this->setAndReturnError($this::INVALID_COMPARE, [
                '%label%' => $this::SIGNS[$this->sign],
                '%expected%' => $this->canonize($this->expected)
            ]);
        }

        return $this::VALID;
    }

    /**
     * Provided value == expected one.
     *
     * @return bool
     */
    protected function eq()
    {
        return $this->value == $this->expected;
    }

    /**
     * Provided value === expected one.
     *
     * @return bool
     */
    protected function seq()
    {
        return $this->value === $this->expected;
    }

    /**
     * Provided value != expected one.
     *
     * @return bool
     */
    protected function neq()
    {
        return $this->value != $this->expected;
    }

    /**
     * Provided value !== expected one.
     *
     * @return bool
     */
    protected function nseq()
    {
        return $this->value !== $this->expected;
    }

    /**
     * Provided value <= expected one.
     *
     * @return bool
     */
    protected function lte()
    {
        return $this->value <= $this->expected;
    }

    /**
     * Provided value >= expected one.
     *
     * @return bool
     */
    protected function gte()
    {
        return $this->value >= $this->expected;
    }

    /**
     * Provided value < expected one.
     *
     * @return bool
     */
    protected function lt()
    {
        return $this->value < $this->expected;
    }

    /**
     * Provided value > expected one.
     *
     * @return bool
     */
    protected function gt()
    {
        return $this->value > $this->expected;
    }
}
