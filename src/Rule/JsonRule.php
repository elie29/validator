<?php

declare(strict_types = 1);

namespace Elie\Validator\Rule;

/**
 * This class verifies that a value is a valid json.
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

    /**
     * Params could have the following structure:
     * [
     *   'required' => {bool:optional},
     *   'trim' => {bool:optional},
     *   'messages' => {array:optional:key/value message patterns}
     * ]
     */
    public function __construct(string $key, $value, array $params = [])
    {
        parent::__construct($key, $value, $params);

        $this->messages = $this->messages + [
            $this::INVALID_JSON => '%key%: %value% is not a valid json format',
        ];
    }

    public function validate(): int
    {
        $run = parent::validate();

        if ($run !== $this::CHECK) {
            return $run;
        }

        if (! $this->isValid()) {
            return $this->setAndReturnError($this::INVALID_JSON);
        }

        return $this::VALID;
    }

    protected function isValid(): bool
    {
        return json_decode($this->value) !== null;
    }
}
