<?php

declare(strict_types = 1);

namespace Elie\Validator\Rule;

/**
 * This class verifies that a value exists in an array.
 */
class RangeRule extends AbstractRule
{

    /**
     * Specific message error code
     */
    public const INVALID_RANGE = 'invalidRange';

    /**#@+
     * Specific options for RangeRule
     */
    public const TRIM = 'trim';
    public const RANGE = 'range';
    /**#@-*/

    /**
     * Range values.
     * Default sets to empty array.
     * @var array
     */
    protected $range = [];

    /**
     * Params could have the following structure:
     * [
     *   'required' => {bool:optional:false by default},
     *   'trim' => {bool:optional:true by default:only if value is string},
     *   'messages' => {array:optional:key/value message patterns},
     *   'range' => {array:optional:empty array by default}
     * ]
     */
    public function __construct(string $key, $value, array $params = [])
    {
        parent::__construct($key, $value, $params);

        if (isset($params[$this::RANGE])) {
            $this->range = $params[$this::RANGE];
        }

        $this->messages = $this->messages + [
            $this::INVALID_RANGE => '%key%: %value% is out of range %range%',
        ];
    }

    public function validate(): int
    {
        $run = parent::validate();

        if ($run !== $this::CHECK) {
            return $run;
        }

        if (! in_array($this->value, $this->range, true)) {
            return $this->setAndReturnError($this::INVALID_RANGE, [
                '%range%' => $this->canonize($this->range)
            ]);
        }

        return $this::VALID;
    }
}
