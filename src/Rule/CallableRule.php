<?php

declare(strict_types = 1);

namespace Elie\Validator\Rule;

/**
 * This class verifies that a value is valid through a callable function.
 */
class CallableRule extends AbstractRule
{

    /**
     * Specific message error code
     */
    public const INVALID_CALLABLE_CHECK = 'invalidCallableCheck';

    /**
     * Specific option for CallableRule
     */
    public const CALLABLE = 'callable';

    /**
     * @var callable _invoke|function($key, $value): bool
     */
    protected $callable;

    /**
     * Params could have the following structure:
     * [
     *   'required' => {bool:optional:false by default},
     *   'trim' => {bool:optional:true by default:only if value is string},
     *   'messages' => {array:optional:key/value message patterns},
     *   'callable' => {callable:required:should receive key/value and return boolean}
     * ]
     */
    public function __construct(string $key, $value, array $params = [])
    {
        parent::__construct($key, $value, $params);

        $this->callable = $params[self::CALLABLE];

        $this->messages = $this->messages + [
            self::INVALID_CALLABLE_CHECK => '%key%: %value% did not pass the callable check',
        ];
    }

    public function validate(): int
    {
        $run = parent::validate();

        if ($run !== $this::CHECK) {
            return $run;
        }

        $callable = $this->callable;

        if (! $callable($this->key, $this->value)) {
            return $this->setAndReturnError($this::INVALID_CALLABLE_CHECK);
        }

        return $this::VALID;
    }
}
