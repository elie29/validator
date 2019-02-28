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
     * List of default message patterns in case of error.
     * supported default keys are %code%, %key% and %value%
     *
     * @var array
     */
    protected $messages = [
        self::UNDEFINDED_CODE => 'Code message %code% is undefined',
        self::EMPTY_KEY => '%key% is required and should not be empty: %value%',
    ];

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

        if (isset($params[$this::REQUIRED])) {
            $this->required = (bool) $params[$this::REQUIRED];
        }

        // trim constant is not available in all rules
        if (isset($params['trim'])) {
            $this->trim = (bool) $params['trim'];
        }

        if (isset($params[$this::MESSAGES])) {
            // replace existant by given messages
            $this->messages = array_merge($this->messages, $params[$this::MESSAGES]);
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
            return $this::CHECK;
        }

        if ($this->isRequired()) {
            // Value is empty and required.
            return $this->setAndReturnError($this::EMPTY_KEY);
        }

        // Value is empty but not required.
        return $this::VALID;
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
     * Empty value is null or '' only.
     *
     * @return bool
     */
    protected function isEmpty(): bool
    {
        return $this->value === null || $this->value === '';
    }

    /**
     * @return int RuleInterface::ERROR
     */
    protected function setAndReturnError(string $errorCode, array $replace = []): int
    {
        $message = $this->messages[$errorCode] ?? $this->messages[$this::UNDEFINDED_CODE];

        // + is used to add unexistant keys
        $replace += [
            '%key%'   => $this->key,
            '%value%' => $this->canonize($this->value),
            '%code%'  => $errorCode,
        ];

        $this->error = str_replace(array_keys($replace), $replace, $message);

        return RuleInterface::ERROR;
    }

    protected function canonize($value): string
    {
        if (is_object($value) && ! in_array('__toString', get_class_methods($value))) {
            return get_class($value) . ' object';
        }

        if (is_array($value)) {
            return str_replace(["\n", "\r"], '', var_export($value, true));
        }

        return (string) $value;
    }
}
