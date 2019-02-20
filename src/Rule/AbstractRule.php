<?php

declare(strict_types = 1);

namespace Elie\Validator\Rule;

use Elie\Validator\Rule\RuleInterface;

abstract class AbstractRule implements RuleInterface
{

    /**
     * @var string
     */
    protected $key;

    /**
     * @var mixed
     */
    protected $value;

    /**
     * Trim value before validation, if value is string.
     * Default sets to true.
     * @var bool
     */
    protected $trim = true;

    /**
     * Contains the error text to be displayed in case an error was found.
     * @var string
     */
    protected $error = '';

    /**
     * If key's context is required or not.
     * If required, value should not be empty.
     * Default sets to false.
     * @var bool
     */
    protected $required = false;

    public function __construct(string $key, $value, array $params = [])
    {
        $this->key = $key;

        if (isset($params['required'])) {
            $this->required = (bool) $params['required'];
        }

        if (isset($params['trim'])) {
            $this->trim = (bool) $params['trim'];
        }

        $this->setValue($value);
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function getError(): string
    {
        return $this->error;
    }

    public function validate(): int
    {
        $this->error = '';

        if (! $this->isEmpty()) {
            // Value is not empty so keep checking
            return RuleInterface::CHECK;
        }

        if ($this->isRequired()) {
            // Value is empty and required.
            $this->error = "{$this->key} is required and should not be empty";
            return RuleInterface::ERROR;
        }

        // Value is empty but not required.
        return RuleInterface::VALID;
    }

    /**
     * Set value properly.
     *
     * @param mixed $value
     */
    protected function setValue($value): void
    {
        if (is_string($value) && $this->trim) {
            $value = trim($value);
        }
        $this->value = $value;
    }

    /**
     * If value is required, it should not be empty.
     * {@link error} is set to blank.
     *
     * @return bool True means that value is required and empty.
     */
    protected function isRequired(): bool
    {
        return $this->required;
    }

    /**
     * If value is equal to 0 or false, we consider that value is not empty.
     *
     * @return bool
     */
    protected function isEmpty(): bool
    {
        return $this->value === null ||
        $this->value === [] ||
        $this->value === '';
    }
}
