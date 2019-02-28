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

    /**#@+
     * Specific option for BooleanRule
     */
    public const TRIM = 'trim';
    public const CAST = 'cast';
    /**#@-*/

    /**
     * Cast the value into boolean
     */
    protected $cast = false;

    /**
     * Params could have the following structure:
     * [
     *   'required' => {bool:optional:false by default},
     *   'trim' => {bool:optional:true by default:only if value is string},
     *   'messages' => {array:optional:key/value message patterns},
     *   'cast' => {bool:optional:cast the value into bool:false by default}
     * ]
     */
    public function __construct(string $key, $value, array $params = [])
    {
        parent::__construct($key, $value, $params);

        if (isset($params[$this::CAST])) {
            $this->cast = (bool) $params[$this::CAST];
        }

        $this->messages = $this->messages + [
            $this::INVALID_BOOLEAN => '%key%: %value% is not a valid boolean',
        ];
    }

    public function getValue()
    {
        if ($this->cast && ! $this->error) {
            return (bool) $this->value;
        }
        return $this->value;
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
