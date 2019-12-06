<?php

declare(strict_types = 1);

namespace Elie\Validator\Rule;

use PHPUnit\Framework\TestCase;

class NumericRuleTest extends TestCase
{

    public function testValidateEmptyValue(): void
    {
        // Empty string value.  Value is not required by default!
        $rule = new NumericRule('name', '');
        $res = $rule->validate();
        assertThat($res, identicalTo(NumericRule::VALID));
        assertThat($rule->getValue(), emptyString());

        // Empty string value with cast
        $rule = new NumericRule('name', '', [
            NumericRule::CAST => true,
        ]);
        $res = $rule->validate();
        assertThat($res, identicalTo(NumericRule::VALID));
        assertThat($rule->getValue(), identicalTo(0));

        // int
        $rule = new NumericRule('name', '12', [
            NumericRule::CAST => true,
        ]);
        $res = $rule->validate();
        assertThat($res, identicalTo(NumericRule::VALID));
        assertThat($rule->getValue(), identicalTo(12));

        // float
        $rule = new NumericRule('name', '12.2', [
            NumericRule::CAST => true,
        ]);
        $res = $rule->validate();
        assertThat($res, identicalTo(NumericRule::VALID));
        assertThat($rule->getValue(), identicalTo(12.2));
    }

    /**
     * @dataProvider getNumericValueProvider
     */
    public function testValidate($value, $params, $expectedResult, $expectedError): void
    {
        $rule = new NumericRule('age', $value, $params);

        $res = $rule->validate();

        assertThat($res, identicalTo($expectedResult));

        assertThat($rule->getError(), identicalTo($expectedError));
    }

    public function getNumericValueProvider(): \Generator
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
}
