<?php

declare(strict_types=1);

namespace Elie\Validator\Rule;

/**
 * This class verifies that a value is valid.
 */
class CompareRule extends AbstractRule implements CompareConstants
{

    /**
     * Sign value supported ==, ===, !=, !==, <=, >=, <, >.
     * Default sets to ==.
     */
    protected string $sign = self::EQ;

    /**
     * Expected value.
     * Default sets to null.
     */
    protected mixed $expected = null;

    /**
     * Params could have the following structure:
     * <code>
     * [
     *   'required' => {bool:optional:false by default},
     *   'trim' => {bool:optional:true by default},
     *   'messages' => {array:optional:key/value message patterns},
     *   'sign' => {string:optional:EQ by default},
     *   'expected' => {mixed:optional:null by default}
     * ]
     * </code>
     */
    public function __construct(int|string $key, mixed $value, array $params = [])
    {
        parent::__construct($key, $value, $params);

        if (isset($params[$this::SIGN])) {
            $this->sign = $params[$this::SIGN];
        }

        if (isset($params['expected'])) {
            $this->expected = $params['expected'];
        }

        $this->messages += [
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
        if (!$this->$method()) {
            return $this->setAndReturnError($this::INVALID_COMPARE, [
                '%label%' => $this::SIGNS[$this->sign],
                '%expected%' => $this->stringify($this->expected),
            ]);
        }

        return $this::VALID;
    }

    /**
     * Provided value == expected one.
     *
     * @return bool
     */
    protected function eq(): bool
    {
        return $this->value == $this->expected;
    }

    /**
     * Provided value === expected one.
     *
     * @return bool
     */
    protected function seq(): bool
    {
        return $this->value === $this->expected;
    }

    /**
     * Provided value != expected one.
     *
     * @return bool
     */
    protected function neq(): bool
    {
        return $this->value != $this->expected;
    }

    /**
     * Provided value !== expected one.
     *
     * @return bool
     */
    protected function nseq(): bool
    {
        return $this->value !== $this->expected;
    }

    /**
     * Provided value <= expected one.
     *
     * @return bool
     */
    protected function lte(): bool
    {
        return $this->value <= $this->expected;
    }

    /**
     * Provided value >= expected one.
     *
     * @return bool
     */
    protected function gte(): bool
    {
        return $this->value >= $this->expected;
    }

    /**
     * Provided value < expected one.
     *
     * @return bool
     */
    protected function lt(): bool
    {
        return $this->value < $this->expected;
    }

    /**
     * Provided value > expected one.
     *
     * @return bool
     */
    protected function gt(): bool
    {
        return $this->value > $this->expected;
    }
}
