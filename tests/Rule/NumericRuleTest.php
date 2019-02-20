<?php

declare(strict_types = 1);

namespace Elie\Validator\Rule;

use PHPUnit\Framework\TestCase;

class NumericRuleTest extends TestCase
{

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
            '', [], RuleInterface::VALID, ''
        ];

        yield 'Given value between 24 and 29' => [
            '25', ['min' => 24, 'max' => 29], RuleInterface::VALID, ''
        ];

        yield 'Given value should be numeric' => [
            'nothing', ['min' => 24, 'max' => 29], RuleInterface::ERROR, 'age: nothing is not numeric'
        ];

        yield 'Given value is less than 24' => [
            '21', ['min' => 24, 'max' => 29], RuleInterface::ERROR, 'age: 21 is less than 24'
        ];

        yield 'Given value is greated than 29' => [
            '30', ['min' => 24, 'max' => 29], RuleInterface::ERROR, 'age: 30 is greater 29'
        ];

        yield 'Given value could not be empty' => [
            '', ['required' => true], RuleInterface::ERROR, 'age is required and should not be empty'
        ];
    }
}
