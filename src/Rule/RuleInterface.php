<?php

declare(strict_types = 1);

namespace Elie\Validator\Rule;

interface RuleInterface extends RuleConstInterface
{

    /**
     * Verify that value is valid.
     *
     * @param string $key Context key.
     * @param mixed $value Value to be tested.
     * @param array  $params Rule could be parameterized.
     *
     * Params could have the following structure:
     * [
     *    'required' => {bool:optional:false by default},
     *    'trim' => {bool:optional:true by default},
     *    'messages' => {array:optional:key/value message patterns}
     * ]
     */
    public function __construct(string $key, $value, array $params = []);

    /**
     * Runs the rule and returns the result.
     *
     * @return int RuleInterface::CHECK to continue checking,
     *    RuleInterface::VALID if value respect the rule and
     *    RuleInterface::ERROR if error occurs.
     */
    public function validate(): int;

    /**
     * Retrieves the error text.
     *
     * @return string
     */
    public function getError(): string;

    /**
     * Retrieve the key.
     *
     * @return string
     */
    public function getKey(): string;

    /**
     * Retrieve the value. Value will be trimmed
     * if trim is set to true.
     *
     * @return mixed
     */
    public function getValue();
}
