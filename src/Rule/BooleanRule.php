<?php

declare(strict_types = 1);

namespace Elie\Validator\Rule;

/**
 * This class verifies that a value is a valid boolean.
 */
class BooleanRule extends AbstractRule
{

    /**
     * Specific message error code
     */
    public const INVALID_BOOLEAN = 'invalidBoolean';

    /**
     * Specific option for BicRule
     */
    public const TRIM = 'trim';

    /**
     * Params could have the following structure:
     * [
     *   'required' => {bool:optional},
     *   'trim' => {bool:optional:only if value is string},
     *   'messages' => {array:optional:key/value message patterns}
     * ]
     */
    public function __construct(string $key, $value, array $params = [])
    {
        parent::__construct($key, $value, $params);

        $this->messages += [
            $this::INVALID_BOOLEAN => '%key%: %value% is not a valid boolean',
        ];
    }

    public function validate(): int
    {
        $run = parent::validate();

        if ($run !== $this::CHECK) {
            return $run;
        }

        if (! $this->isBool()) {
            return $this->setAndReturnError($this::INVALID_BOOLEAN);
        }

        return $this::VALID;
    }

    protected function isBool(): bool
    {
        $val = $this->value;
        return
            $val === 0 || $val === 1 ||
            $val === '0' || $val === '1' ||
            $val === true || $val === false;
    }
}
