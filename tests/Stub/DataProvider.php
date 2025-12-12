<?php

namespace Elie\Validator\Stub;

use Elie\Validator\Rule\ArrayRule;
use Elie\Validator\Rule\MultipleAndRule;
use Elie\Validator\Rule\MultipleOrRule;
use Elie\Validator\Rule\NumericRule;
use Elie\Validator\Rule\RangeRule;
use Elie\Validator\Rule\RuleInterface;
use Elie\Validator\Rule\StringRule;
use Generator;
use stdClass;

class DataProvider
{
    public static function getValidatorProvider(): Generator
    {
        yield 'Age and name are valid' => [
            // data
            [
                'age' => 25,
                'name' => 'Ben',
            ],
            // rules
            [
                ['age', NumericRule::class, 'min' => 5, 'max' => 65],
                ['name', StringRule::class, 'min' => 3, 'max' => 30],
            ],
            // expectedResult
            true,
            // errorsSize
            0,
        ];

        yield 'Validate with multiple and rule' => [
            [
                'age' => 25,
            ],
            [
                ['age', MultipleAndRule::class, MultipleAndRule::REQUIRED => true, MultipleAndRule::RULES => [
                    [NumericRule::class, NumericRule::MIN => 14],
                    [RangeRule::class, RangeRule::RANGE => [25, 26]],
                ]],
            ],
            true,
            0,
        ];

        yield 'Validate with multiple or rule' => [
            [
                'foo' => 'bar',
            ],
            [
                ['foo', MultipleOrRule::class, MultipleOrRule::REQUIRED => true, MultipleOrRule::RULES => [
                    [NumericRule::class, NumericRule::MIN => 14],
                    [StringRule::class, StringRule::MIN => 1],
                ]],
            ],
            true,
            0,
        ];

        yield 'Age is not valid' => [
            [
                'age' => 25,
            ],
            [
                ['age', NumericRule::class, 'min' => 26, 'max' => 65],
            ],
            false,
            1,
        ];
        yield 'Key with numeric value' => [
            [
                0 => 25,
                1 => 'Test',
            ],
            [
                [0, NumericRule::class, 'min' => 22, 'max' => 65],
                [1, StringRule::class, 'min' => 0, 'max' => 10],
            ],
            true,
            0,
        ];
        yield 'Key with index context' => [
            [
                28,
                'Test2',
            ],
            [
                [0, NumericRule::class, 'min' => 22, 'max' => 65],
                [1, StringRule::class, 'min' => 0, 'max' => 10],
            ],
            true,
            0,
        ];
    }

    public static function getValueProvider(): Generator
    {
        yield 'Trim is true but required is false by default' => [
            '  ', // data
            [], // rules
            RuleInterface::VALID, // expectedResult
            '', // expectedError
        ];

        yield 'Value could be empty if not required' => [
            '  ',
            [StringRule::TRIM => true, RuleInterface::REQUIRED => false],
            RuleInterface::VALID,
            '',
        ];

        yield 'Required value could be one character space' => [
            ' ',
            [StringRule::TRIM => false, RuleInterface::REQUIRED => true],
            RuleInterface::CHECK,
            '',
        ];

        yield 'Required value could be false' => [
            false,
            [RuleInterface::REQUIRED => true],
            RuleInterface::CHECK,
            '',
        ];

        yield 'Required value should not be an empty string' => [
            '',
            [RuleInterface::REQUIRED => true],
            RuleInterface::ERROR,
            'key is required and should not be empty: ',
        ];

        yield 'Required value should not be null' => [
            null,
            [RuleInterface::REQUIRED => true],
            RuleInterface::ERROR,
            'key is required and should not be empty: <NULL>',
        ];
    }

    public static function getArrayValueProvider(): Generator
    {
        yield 'Given value could be empty' => [
            [], // data
            [], // rules
            RuleInterface::VALID, // expectedResult
            '', // error
        ];

        yield 'Given value between 4 and 8' => [
            ['Peter', 'Ben', 'Harold'],
            [ArrayRule::MIN => 3, ArrayRule::MAX => 8],
            RuleInterface::VALID,
            '',
        ];

        yield 'Given value should be more than 3' => [
            ['Peter', 'Ben', 'Harold'],
            [ArrayRule::MIN => 3],
            RuleInterface::VALID,
            '',
        ];

        yield 'Given value should be an array' => [
            new stdClass(),
            [],
            RuleInterface::ERROR,
            'name does not have an array value: stdClass object',
        ];

        yield 'Given value is not between 4 and 8' => [
            ['Peter', 'Ben', 'Harold'],
            [ArrayRule::MIN => 4, ArrayRule::MAX => 8],
            RuleInterface::ERROR,
            "name: The length of array (  0 => 'Peter',  1 => 'Ben',  2 => 'Harold',) is not between 4 and 8",
        ];

        yield 'Required value should not be an empty array' => [
            [],
            [RuleInterface::REQUIRED => true],
            RuleInterface::ERROR,
            'name is required and should not be empty: array ()',
        ];

        yield 'Required value should not be an empty array, with specific message' => [
            [],
            [RuleInterface::REQUIRED => true, 'messages' => [
                RuleInterface::EMPTY_KEY => '%key% is required. `%value%` is empty!',
            ]],
            RuleInterface::ERROR,
            'name is required. `array ()` is empty!',
        ];
    }
}