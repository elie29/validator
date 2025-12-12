<?php

declare(strict_types=1);

namespace Elie\Validator\Rule;

use Generator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class CompareRuleTest extends TestCase
{

    public static function getCompareValueProvider(): Generator
    {
        yield 'Given value could be empty' => [
            '', // value
            [], // params
            RuleInterface::VALID, // expectedResult
            '', // expectedError
        ];

        yield 'Given value should be equal to 5' => [
            '5',
            [CompareConstants::SIGN => CompareConstants::EQ, CompareConstants::EXPECTED => 5],
            RuleInterface::VALID,
            '',
        ];

        yield 'Given value should be same as 5' => [
            '5',
            [CompareConstants::SIGN => CompareConstants::SEQ, CompareConstants::EXPECTED => '5'],
            RuleInterface::VALID,
            '',
        ];

        yield 'Given value should be same as [5, 4, false]' => [
            [5, 4, false],
            [CompareConstants::SIGN => CompareConstants::SEQ,
                CompareConstants::EXPECTED => [5, 4, false]], RuleInterface::VALID,
            '',
        ];

        yield 'Given value should not be equal to 5' => [
            '15',
            [CompareConstants::SIGN => CompareConstants::NEQ, CompareConstants::EXPECTED => 5],
            RuleInterface::VALID,
            '',
        ];

        yield 'Given value should not be same as [5, 4, 0]' => [
            [5, 4, false],
            [CompareConstants::SIGN => CompareConstants::NSEQ,
                CompareConstants::EXPECTED => [5, 4, 0]], RuleInterface::VALID,
            '',
        ];

        yield 'Given value should not be same as 5' => [
            '25',
            [CompareConstants::SIGN => CompareConstants::NSEQ, CompareConstants::EXPECTED => '5'],
            RuleInterface::VALID,
            '',
        ];

        yield 'Given value should be less than5' => [
            '4',
            [CompareConstants::SIGN => CompareConstants::LT, CompareConstants::EXPECTED => 5],
            RuleInterface::VALID,
            '',
        ];

        yield 'Given value should be less or equal to 5' => [
            '5',
            [CompareConstants::SIGN => CompareConstants::LTE, CompareConstants::EXPECTED => '5'],
            RuleInterface::VALID,
            '',
        ];

        yield 'Given value should be greater than 5' => [
            '6',
            [CompareConstants::SIGN => CompareConstants::GT, CompareConstants::EXPECTED => 5],
            RuleInterface::VALID,
            '',
        ];

        yield 'Given value should be geater or equal to 5' => [
            '5',
            [CompareConstants::SIGN => CompareConstants::GTE, CompareConstants::EXPECTED => '5'],
            RuleInterface::VALID,
            '',
        ];

        yield 'Given value should not be less than 5' => [
            '2',
            [CompareConstants::SIGN => CompareConstants::GTE, CompareConstants::EXPECTED => '5'],
            RuleInterface::ERROR,
            'name: 2 is not greater or equal to 5',
        ];
    }

    #[DataProvider('getCompareValueProvider')]
    public function testValidate($value, $params, $expectedResult, $expectedError): void
    {
        $rule = new CompareRule('name', $value, $params);

        $res = $rule->validate();

        $this->assertSame($expectedResult, $res);

        $this->assertSame($expectedError, $rule->getError());
    }
}
