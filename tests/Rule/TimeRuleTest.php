<?php

declare(strict_types=1);

namespace Elie\Validator\Rule;

use Generator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class TimeRuleTest extends TestCase
{

    public static function getTimeValueProvider(): Generator
    {
        yield 'Given value could be empty' => [
            '',
            RuleInterface::VALID,
            '',
        ];

        yield 'Given time 22:25 should be valid' => [
            '22:25',
            RuleInterface::VALID,
            '',
        ];

        yield 'Given time 22:25:38 should be valid' => [
            '22:25:38',
            RuleInterface::VALID,
            '',
        ];

        yield 'Given time 8:2:4 should be valid' => [
            '8:2:4',
            RuleInterface::VALID,
            '',
        ];

        yield 'Given time 25:2 should not be valid' => [
            '25:2',
            RuleInterface::ERROR,
            'time: 25:2 is not a valid time',
        ];

        yield 'Given time 21 should not be valid' => [
            '21',
            RuleInterface::ERROR,
            'time: 21 is not a valid time',
        ];

        yield 'Given time 20:2:3:6 should not be valid' => [
            '20:2:3:6',
            RuleInterface::ERROR,
            'time: 20:2:3:6 is not a valid time',
        ];
    }

    #[DataProvider('getTimeValueProvider')]
    public function testValidate($value, $expectedResult, $expectedError): void
    {
        $rule = new TimeRule('time', $value);

        $res = $rule->validate();

        $this->assertSame($expectedResult, $res);

        $this->assertSame($expectedError, $rule->getError());
    }
}
