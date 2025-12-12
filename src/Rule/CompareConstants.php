<?php

declare(strict_types=1);

namespace Elie\Validator\Rule;

interface CompareConstants
{

    /**#@+
     * Supported sign
     */
    public const EQ = 'eq';   // ==
    public const SEQ = 'seq';  // ===
    public const NEQ = 'neq';  // !=
    public const NSEQ = 'nseq'; // !==
    public const LTE = 'lte';  // <=
    public const GTE = 'gte';  // >=
    public const LT = 'lt';   // <
    public const GT = 'gt';   // >
    /**#@-*/

    /**
     * Supported signs
     */
    public const SIGNS = [
        self::EQ => 'equal to',
        self::SEQ => 'same as',
        self::NEQ => 'not equal to',
        self::NSEQ => 'not same as',
        self::LTE => 'less or equal to',
        self::GTE => 'greater or equal to',
        self::LT => 'less than',
        self::GT => 'greater than',
    ];

    /**
     * Specific message error code
     */
    public const INVALID_COMPARE = 'invalidCompare';

    /**#@+
     * Specific options for CompareRule
     */
    public const TRIM = 'trim';
    public const SIGN = 'sign';
    public const EXPECTED = 'expected';
    /**#@-*/
}
