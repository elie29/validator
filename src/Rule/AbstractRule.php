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
        self::INVALID_ARRAY => '%key% does not have an array value: %value%',
        self::INVALID_ARRAY_LENGTH => '%key%: The length of %value% is not between %min% and %max%',
        self::INVALID_BIC_LIMIT => '%key%: %value% has an invalid length',
        self::INVALID_BIC_UPPER => '%key%: %value% should be uppercase',
        self::INVALID_BIC_ALNUM => '%key%: %value% should be alphanumeric',
        self::INVALID_BIC_BC => '%key%: %value% has an invalid bank code',
        self::INVALID_BIC_CC => '%key%: %value% has an invalid country code',
        self::INVALID_BOOL => '%key%: %value% is not a valid boolean',
        self::INVALID_DATE => '%key%: %value% is not a valid date',
        self::INVALID_DATE_FORMAT => '%key%: %value% does not have a valid format: %format% or separator: %separator%',
        self::INVALID_COMPARE => '%key%: %value% is not %label% %expected%',
        self::INVALID_EMAIL => '%key%: %value% is not a valid email',
        self::INVALID_IP => '%key%: %value% is not a valid IP',
        self::INVALID_IP_FLAG => 'Filter IP flag: %flag% is not valid',
        self::INVALID_JSON => '%key%: %value% is not a valid json format',
        self::INVALID_PATTERN => '%key%: %value% does not match %pattern%',
        self::INVALID_NUMERIC => '%key%: %value% is not numeric',
        self::INVALID_NUMERIC_LT => '%key%: %value% is less than %min%',
        self::INVALID_NUMERIC_GT => '%key%: %value% is greater than %max%',
        self::INVALID_RANGE => '%key%: %value% is out of range %range%',
        self::INVALID_STRING => '%key% does not have a string value: %value%',
        self::INVALID_STRING_LENGTH => '%key%: The length of %value% is not between %min% and %max%',
        self::INVALID_TIME => '%key%: %value% is not a valid time',
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

        if (isset($params['required'])) {
            $this->required = (bool) $params['required'];
        }

        if (isset($params['trim'])) {
            $this->trim = (bool) $params['trim'];
        }

        if (isset($params['messages'])) {
            // replace existant by given messages
            $this->messages = array_merge($this->messages, $params['messages']);
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
            return $this->setAndReturnError(self::EMPTY_KEY);
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

    /**
     * @return int RuleInterface::ERROR
     */
    protected function setAndReturnError(string $errorCode, array $replace = []): int
    {
        $message = $this->messages[$errorCode] ?? $this->messages[self::UNDEFINDED_CODE];

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
