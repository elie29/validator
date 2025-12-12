<?php

declare(strict_types=1);

namespace Elie\Validator\Rule;

/**
 * This class verifies that a value is a valid JSON.
 */
class JsonRule extends AbstractRule
{

    /**
     * Specific message error code
     */
    public const INVALID_JSON = 'invalidJsonFormat';

    /**
     * Specific options for JsonRule
     */
    public const TRIM = 'trim';
    public const DECODE = 'decode';

    /**
     * Decode the value to be returned.
     */
    protected bool $decode = false;

    /**
     * Params could have the following structure:
     * <code>
     * [
     *   'required' => {bool:optional:false by default},
     *   'trim' => {bool:optional:true by default},
     *   'messages' => {array:optional:key/value message patterns},
     *   'decode' => {bool:optional:false by default:decode the value}
     * ]
     * </code>
     */
    public function __construct(int|string $key, mixed $value, array $params = [])
    {
        parent::__construct($key, $value, $params);

        if (isset($params[$this::DECODE])) {
            $this->decode = (bool)$params[$this::DECODE];
        }

        $this->messages += [
            $this::INVALID_JSON => '%key%: %value% is not a valid json format',
        ];
    }

    public function getValue(): mixed
    {
        if ($this->error || $this->value || !$this->decode) {
            return $this->value;
        }

        // only if no error and decoded is requested for an empty value
        return [];
    }

    public function validate(): int
    {
        $run = parent::validate();

        if ($run !== $this::CHECK) {
            return $run;
        }

        if (!$this->isValid()) {
            return $this->setAndReturnError($this::INVALID_JSON);
        }

        return $this::VALID;
    }

    protected function isValid(): bool
    {
        $decoded = json_decode($this->value, true);

        if ($decoded === null) {
            return false;
        }

        $this->value = $this->decode ? $decoded : $this->value;

        return true;
    }
}
