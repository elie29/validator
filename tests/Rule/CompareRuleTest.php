<?php

declare(strict_types = 1);

namespace Elie\Validator\Rule;

use PHPUnit\Framework\TestCase;

class CompareRuleTest extends TestCase
{

    /**
     * @dataProvider getCompareValueProvider
     */
    public function testValidate($value, $params, $expectedResult, $expectedError): void
    {
        $rule = new CompareRule('name', $value, $params);

        $res = $rule->validate();

        assertThat($res, identicalTo($expectedResult));

        assertThat($rule->getError(), identicalTo($expectedError));
    }

    public function getCompareValueProvider(): \Generator
    {
        yield 'Given value could be empty' => [
            '', [], RuleInterface::VALID, ''
        ];

        yield 'Given value should be equal to 5' => [
            '5', ['sign' => RuleInterface::EQ, 'expected' => 5], RuleInterface::VALID, ''
        ];

        yield 'Given value should be same as 5' => [
            '5', ['sign' => RuleInterface::SEQ, 'expected' => '5'], RuleInterface::VALID, ''
        ];

        yield 'Given value should be same as [5, 4, false]' => [
            [5, 4, false], ['sign' => RuleInterface::SEQ, 'expected' => [5, 4, false]], RuleInterface::VALID, ''
        ];

        yield 'Given value should not be equal to 5' => [
            '15', ['sign' => RuleInterface::NEQ, 'expected' => 5], RuleInterface::VALID, ''
        ];

        yield 'Given value should not be same as [5, 4, 0]' => [
            [5, 4, false], ['sign' => RuleInterface::NSEQ, 'expected' => [5, 4, 0]], RuleInterface::VALID, ''
        ];

        yield 'Given value should not be same as 5' => [
            '25', ['sign' => RuleInterface::NSEQ, 'expected' => '5'], RuleInterface::VALID, ''
        ];

        yield 'Given value should be less than5' => [
            '4', ['sign' => RuleInterface::LT, 'expected' => 5], RuleInterface::VALID, ''
        ];

        yield 'Given value should be less or equal to 5' => [
            '5', ['sign' => RuleInterface::LTE, 'expected' => '5'], RuleInterface::VALID, ''
        ];

        yield 'Given value should be greater than 5' => [
            '6', ['sign' => RuleInterface::GT, 'expected' => 5], RuleInterface::VALID, ''
        ];

        yield 'Given value should be geater or equal to 5' => [
            '5', ['sign' => RuleInterface::GTE, 'expected' => '5'], RuleInterface::VALID, ''
        ];

        yield 'Given value should be geater or equal to 5' => [
            '2', ['sign' => RuleInterface::GTE, 'expected' => '5'],
            RuleInterface::ERROR, 'name: 2 is not greater or equal to 5'
        ];
    }
}
