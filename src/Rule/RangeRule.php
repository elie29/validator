<?php

declare(strict_types = 1);

namespace Elie\Validator\Rule;

/**
 * This class verifies that a value exists in an array.
 */
class RangeRule extends AbstractRule
{

    /**
     * Range values.
     * Default sets to empty array.
     * @var array
     */
    protected $range = [];

    /**
     * Params could have the following structure:
     * [
     *   'required' => {bool:optional},
     *   'trim' => {bool:optional},
     *   'messages' => {array:optional:key/value message patterns},
     *   'range' => {array:optional:empty array by default}
     * ]
     */
    public function __construct(string $key, $value, array $params = [])
    {
        parent::__construct($key, $value, $params);

        if (isset($params['range'])) {
            $this->range = $params['range'];
        }
    }

    public function validate(): int
    {
        $run = parent::validate();

        if ($run !== RuleInterface::CHECK) {
            return $run;
        }

        if (! in_array($this->value, $this->range, true)) {
            return $this->setAndReturnError(self::INVALID_RANGE, [
                '%range%' => $this->canonize($this->range)
            ]);
        }

        return RuleInterface::VALID;
    }
}
