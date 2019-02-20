<?php

declare(strict_types = 1);

namespace Elie\Validator\Rule;

interface RuleInterface
{

    /**#@+
     * Run returns constant
     */
    public const ERROR = 0;
    public const VALID = 1;
    public const CHECK = 2;
    /**#@-*/

    /**#@+
     * Supported sign
     */
    public const EQ   = 'eq';   // ==
    public const SEQ  = 'seq';  // ===
    public const NEQ  = 'neq';  // !=
    public const NSEQ = 'nseq'; // !==
    public const LTE  = 'lte';  // <=
    public const GTE  = 'gte';  // >=
    public const LT   = 'lt';   // <
    public const GT   = 'gt';   // >
    /**#@-*/

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
     *    'trim' => {bool:optional:true by default}
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
