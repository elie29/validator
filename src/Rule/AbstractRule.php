<?php

declare(strict_types=1);

namespace Elie\Validator\Rule;

abstract class AbstractRule implements RuleInterface
{

    protected string|int $key;

    protected mixed $value;

    /**
     * Trim value before validation, if value is string.
     * Default sets to true.
     */
    protected bool $trim = true;

    /**
     * Contains the error text to be displayed in case an error was found.
     */
    protected string $error = '';

    /**
     * List of default message patterns in case of error.
     * Supported default keys are %code%, %key% and %value%
     */
    protected array $messages = [
        self::UNDEFINED_CODE => 'Code message %code% is undefined',
        self::EMPTY_KEY => '%key% is required and should not be empty: %value%',
    ];

    /**
     * If the key's context is required or not.
     * If required, the value should not be empty.
     * Default sets to false.
     */
    protected bool $required = false;

    /**
     * Default constructor to set common params.
     *
     * @param int|string $key Key is used in the error message.
     * @param mixed $value Value to be validated.
     * @param array $params Default params.
     */
    public function __construct(int|string $key, mixed $value, array $params = [])
    {
        $this->key = $key;

        if (isset($params[$this::REQUIRED])) {
            $this->required = (bool)$params[$this::REQUIRED];
        }

        // trim constant is not available in all rules
        if (isset($params['trim'])) {
            $this->trim = (bool)$params['trim'];
        }

        if (isset($params[$this::MESSAGES])) {
            // replace existent by given messages
            $this->messages = array_merge($this->messages, $params[$this::MESSAGES]);
        }

        $this->setValue($value);
    }

    public function getKey(): int|string
    {
        return $this->key;
    }

    public function getValue(): mixed
    {
        return $this->value;
    }

    public function setValue(mixed $value): void
    {
        if (is_string($value) && $this->trim) {
            $value = trim($value);
        }

        $this->value = $value;
    }

    public function getError(): string
    {
        return $this->error;
    }

    public function validate(): int
    {
        $this->error = '';

        if (!$this->isEmpty()) {
            // Value is not empty, so keep checking
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
     * If a value is required, it should not be empty.
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
        $message = $this->messages[$errorCode] ?? $this->messages[$this::UNDEFINED_CODE];

        // + is used to add non-existent keys
        $replace += [
            '%key%' => $this->key,
            '%value%' => $this->stringify($this->value),
            '%code%' => $errorCode,
        ];

        $this->error = str_replace(array_keys($replace), $replace, $message);

        return RuleInterface::ERROR;
    }

    protected function stringify($value): string
    {
        if (is_object($value) && !in_array('__toString', get_class_methods($value))) {
            return get_class($value) . ' object';
        }

        if (is_array($value)) {
            return str_replace(["\n", "\r"], '', var_export($value, true));
        }

        return $this->fromScalar($value);
    }

    protected function fromScalar($value): string
    {
        if (is_bool($value)) {
            return $value ? '<TRUE>' : '<FALSE>';
        }

        if ($value === null) {
            return '<NULL>';
        }

        return (string)$value;
    }
}
