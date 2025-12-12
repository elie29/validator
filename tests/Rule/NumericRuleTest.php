<?php

declare(strict_types=1);

namespace Elie\Validator\Rule;

use Generator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class NumericRuleTest extends TestCase
{

    public static function getNumericValueProvider(): Generator
    {
        yield 'Given value could be empty' => [
            '',
            [],
            RuleInterface::VALID,
            '',
        ];

        yield 'Given value between 24 and 29' => [
            '25',
            [NumericRule::MIN => 24, NumericRule::MAX => 29],
            RuleInterface::VALID,
            '',
        ];

        yield 'Given value should be numeric' => [
            'nothing',
            [NumericRule::MIN => 24, NumericRule::MAX => 29],
            RuleInterface::ERROR,
            'age: nothing is not numeric',
        ];

        yield 'Given value is less than 24' => [
            '21',
            [NumericRule::MIN => 24, NumericRule::MAX => 29],
            RuleInterface::ERROR,
            'age: 21 is less than 24',
        ];

        yield 'Given value is greater than 29' => [
            '30',
            [NumericRule::MIN => 24, NumericRule::MAX => 29],
            RuleInterface::ERROR,
            'age: 30 is greater than 29',
        ];

        yield 'Given value could not be empty' => [
            '',
            ['required' => true],
            RuleInterface::ERROR,
            'age is required and should not be empty: ',
        ];
    }

    public function testValidateEmptyValue(): void
    {
        // Empty string value.  Value is not required by default!
        $rule = new NumericRule('name', '');
        $res = $rule->validate();
        $this->assertSame(RuleInterface::VALID, $res);
        $this->assertSame('', $rule->getValue());

        // Empty string value with cast
        $rule = new NumericRule('name', '', [
            NumericRule::CAST => true,
        ]);
        $res = $rule->validate();
        $this->assertSame(RuleInterface::VALID, $res);
        $this->assertSame(0, $rule->getValue());

        // int
        $rule = new NumericRule('name', '12', [
            NumericRule::CAST => true,
        ]);
        $res = $rule->validate();
        $this->assertSame(RuleInterface::VALID, $res);
        $this->assertSame(12, $rule->getValue());

        // float
        $rule = new NumericRule('name', '12.2', [
            NumericRule::CAST => true,
        ]);
        $res = $rule->validate();
        $this->assertSame(RuleInterface::VALID, $res);
        $this->assertSame(12.2, $rule->getValue());
    }

    #[DataProvider('getNumericValueProvider')]
    public function testValidate($value, $params, $expectedResult, $expectedError): void
    {
        $rule = new NumericRule('age', $value, $params);

        $res = $rule->validate();

        $this->assertSame($expectedResult, $res);

        $this->assertSame($expectedError, $rule->getError());
    }
}
