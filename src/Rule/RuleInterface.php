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
     * Supported signs
     */
    public const SIGNS = [
        self::EQ   => 'equal to',
        self::SEQ  => 'same as',
        self::NEQ  => 'not equal to',
        self::NSEQ => 'not same as',
        self::LTE  => 'less or equal to',
        self::GTE  => 'greater or equal to',
        self::LT   => 'less than',
        self::GT   => 'greater than',
    ];

    /**#@+
     * Supported error message code
     */
    public const UNDEFINDED_CODE = 'undefindedCode';
    public const EMPTY_KEY = 'emptyKey';
    public const INVALID_ARRAY = 'invalidArray';
    public const INVALID_ARRAY_LENGTH = 'invalidArrayLength';
    public const INVALID_BIC_LIMIT = 'invalidBicLimit';
    public const INVALID_BIC_UPPER = 'invalidBicUpper';
    public const INVALID_BIC_ALNUM = 'invalidBicAlnum';
    public const INVALID_BIC_BC = 'invalidBicBC';
    public const INVALID_BIC_CC = 'invalidBicCC';
    public const INVALID_BOOL = 'invalidBoolean';
    public const INVALID_DATE = 'invalidDate';
    public const INVALID_DATE_FORMAT = 'invalidDateFormat';
    public const INVALID_COMPARE = 'invalidComprare';
    public const INVALID_EMAIL = 'invalidEmail';
    public const INVALID_IP = 'invalidIP';
    public const INVALID_IP_FLAG = 'invalidIPFlag';
    public const INVALID_JSON = 'invalidJsonFormat';
    public const INVALID_PATTERN = 'invalidPattern';
    public const INVALID_NUMERIC = 'invalidNumeric';
    public const INVALID_NUMERIC_LT = 'invalidNumericLessThan';
    public const INVALID_NUMERIC_GT = 'invalidNumericGreaterThan';
    public const INVALID_RANGE = 'invalidRange';
    public const INVALID_STRING = 'invalidString';
    public const INVALID_STRING_LENGTH = 'invalidStringLength';
    public const INVALID_TIME = 'invalidTime';
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
