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
            '',
            [],
            CompareRule::VALID,
            '',
        ];

        yield 'Given value should be equal to 5' => [
            '5',
            [CompareRule::SIGN => CompareRule::EQ, CompareRule::EXPECTED => 5],
            CompareRule::VALID,
            '',
        ];

        yield 'Given value should be same as 5' => [
            '5',
            [CompareRule::SIGN => CompareRule::SEQ, CompareRule::EXPECTED => '5'],
            CompareRule::VALID,
            '',
        ];

        yield 'Given value should be same as [5, 4, false]' => [
            [5, 4, false],
            [CompareRule::SIGN => CompareRule::SEQ,
                CompareRule::EXPECTED => [5, 4, false]], CompareRule::VALID,
            '',
        ];

        yield 'Given value should not be equal to 5' => [
            '15',
            [CompareRule::SIGN => CompareRule::NEQ, CompareRule::EXPECTED => 5],
            CompareRule::VALID,
            '',
        ];

        yield 'Given value should not be same as [5, 4, 0]' => [
            [5, 4, false],
            [CompareRule::SIGN => CompareRule::NSEQ,
                CompareRule::EXPECTED => [5, 4, 0]], CompareRule::VALID,
            '',
        ];

        yield 'Given value should not be same as 5' => [
            '25',
            [CompareRule::SIGN => CompareRule::NSEQ, CompareRule::EXPECTED => '5'],
            CompareRule::VALID,
            '',
        ];

        yield 'Given value should be less than5' => [
            '4',
            [CompareRule::SIGN => CompareRule::LT, CompareRule::EXPECTED => 5],
            CompareRule::VALID,
            '',
        ];

        yield 'Given value should be less or equal to 5' => [
            '5',
            [CompareRule::SIGN => CompareRule::LTE, CompareRule::EXPECTED => '5'],
            CompareRule::VALID,
            '',
        ];

        yield 'Given value should be greater than 5' => [
            '6',
            [CompareRule::SIGN => CompareRule::GT, CompareRule::EXPECTED => 5],
            CompareRule::VALID,
            '',
        ];

        yield 'Given value should be geater or equal to 5' => [
            '5',
            [CompareRule::SIGN => CompareRule::GTE, CompareRule::EXPECTED => '5'],
            CompareRule::VALID,
            '',
        ];

        yield 'Given value should not be less than 5' => [
            '2',
            [CompareRule::SIGN => CompareRule::GTE, CompareRule::EXPECTED => '5'],
            CompareRule::ERROR,
            'name: 2 is not greater or equal to 5',
        ];
    }
}
