<?php

declare(strict_types=1);

namespace Elie\Validator\Rule;

use Generator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class RangeRuleTest extends TestCase
{

    public static function getRangeValueProvider(): Generator
    {
        yield 'Given value could be empty' => [
            '',
            [],
            RuleInterface::VALID,
            '',
        ];

        yield 'Given value foo should be valid' => [
            'foo',
            [RangeRule::RANGE => ['foo', 'bar']],
            RuleInterface::VALID,
            '',
        ];

        yield 'Given value foot should not be valid' => [
            'foot',
            [RangeRule::RANGE => ['foo', 'bar']],
            RuleInterface::ERROR,
            'value: foot is out of range array (  0 => \'foo\',  1 => \'bar\',)',
        ];

        yield 'Given value false should not be valid' => [
            '0',
            [RangeRule::RANGE => [0, 'false']],
            RuleInterface::ERROR,
            'value: 0 is out of range array (  0 => 0,  1 => \'false\',)',
        ];
    }

    #[DataProvider('getRangeValueProvider')]
    public function testValidate($value, $params, $expectedResult, $expectedError): void
    {
        $rule = new RangeRule('value', $value, $params);

        $res = $rule->validate();

        $this->assertSame($expectedResult, $res);

        $this->assertSame($expectedError, $rule->getError());
    }
}
