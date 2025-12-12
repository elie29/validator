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

        yield 'Given value should be greater or equal to 5' => [
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

    public function testTypeComparisonStringVsInt(): void
    {
        // String '5' == int 5 (loose comparison)
        $rule = new CompareRule('value', '5', [
            CompareConstants::SIGN => CompareConstants::EQ,
            CompareConstants::EXPECTED => 5,
        ]);

        $this->assertSame(RuleInterface::VALID, $rule->validate());

        // String '5' !== int 5 (strict comparison)
        $rule = new CompareRule('value', '5', [
            CompareConstants::SIGN => CompareConstants::SEQ,
            CompareConstants::EXPECTED => 5,
        ]);

        $this->assertSame(RuleInterface::ERROR, $rule->validate());
    }

    public function testTypeComparisonArrays(): void
    {
        // Arrays with the same values and order should be strictly equal
        $rule = new CompareRule('data', [1, 2, 3], [
            CompareConstants::SIGN => CompareConstants::SEQ,
            CompareConstants::EXPECTED => [1, 2, 3],
        ]);

        $this->assertSame(RuleInterface::VALID, $rule->validate());

        // Arrays with different order should not be equal
        $rule = new CompareRule('data', [1, 2, 3], [
            CompareConstants::SIGN => CompareConstants::SEQ,
            CompareConstants::EXPECTED => [3, 2, 1],
        ]);

        $this->assertSame(RuleInterface::ERROR, $rule->validate());
    }

    public function testTypeComparisonBooleanVsInt(): void
    {
        // true == 1 (loose comparison)
        $rule = new CompareRule('flag', true, [
            CompareConstants::SIGN => CompareConstants::EQ,
            CompareConstants::EXPECTED => 1,
        ]);

        $this->assertSame(RuleInterface::VALID, $rule->validate());

        // true !== 1 (strict comparison)
        $rule = new CompareRule('flag', true, [
            CompareConstants::SIGN => CompareConstants::SEQ,
            CompareConstants::EXPECTED => 1,
        ]);

        $this->assertSame(RuleInterface::ERROR, $rule->validate());
    }

    public function testNotStrictlyEqualComparison(): void
    {
        // Test NSEQ - not strictly equal
        $rule = new CompareRule('value', '5', [
            CompareConstants::SIGN => CompareConstants::NSEQ,
            CompareConstants::EXPECTED => 5,
        ]);

        $this->assertSame(RuleInterface::VALID, $rule->validate());

        $rule = new CompareRule('value', 5, [
            CompareConstants::SIGN => CompareConstants::NSEQ,
            CompareConstants::EXPECTED => 5,
        ]);

        $this->assertSame(RuleInterface::ERROR, $rule->validate());
    }

    public function testComparisonWithNull(): void
    {
        // null == false (loose comparison)
        $rule = new CompareRule('value', null, [
            CompareConstants::SIGN => CompareConstants::EQ,
            CompareConstants::EXPECTED => false,
        ]);

        $this->assertSame(RuleInterface::VALID, $rule->validate());

        // null !== false, but null is considered empty
        $rule = new CompareRule('value', null, [
            CompareConstants::SIGN => CompareConstants::SEQ,
            CompareConstants::EXPECTED => false,
        ]);

        $this->assertSame(RuleInterface::VALID, $rule->validate());
    }
}
