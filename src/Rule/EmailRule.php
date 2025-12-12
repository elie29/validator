<?php

declare(strict_types=1);

namespace Elie\Validator\Rule;

/**
 * This class verifies that a value is a valid email.
 */
class EmailRule extends AbstractRule
{

    /**
     * Specific message error code
     */
    public const INVALID_EMAIL = 'invalidEmail';

    /**
     * Specific options for EmailRule
     */
    public const TRIM = 'trim';

    /**
     * Params could have the following structure:
     * <code>
     * [
     *   'required' => {bool:optional:false by default},
     *   'trim' => {bool:optional:true by default},
     *   'messages' => {array:optional:key/value message patterns}
     * ]
     * </code>
     */
    public function __construct(int|string $key, mixed $value, array $params = [])
    {
        parent::__construct($key, $value, $params);

        $this->messages += [
            self::INVALID_EMAIL => '%key%: %value% is not a valid email',
        ];
    }

    public function validate(): int
    {
        $run = parent::validate();

        if ($run !== $this::CHECK) {
            return $run;
        }

        if (!$this->isValid()) {
            return $this->setAndReturnError(self::INVALID_EMAIL);
        }

        return $this::VALID;
    }

    protected function isValid(): bool
    {
        return filter_var($this->value, FILTER_VALIDATE_EMAIL) !== false;
    }
}
