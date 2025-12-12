<?php

declare(strict_types=1);

namespace Elie\Validator\Rule;

use Generator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class MatchRuleTest extends TestCase
{

    public static function getMatchValueProvider(): Generator
    {
        yield 'Given value could be empty' => [
            '',
            [MatchRule::PATTERN => ''],
            RuleInterface::VALID,
            '',
        ];

        yield 'Given value test should be valid' => [
            'test',
            [MatchRule::PATTERN => '/^[a-z]+$/'],
            RuleInterface::VALID,
            '',
        ];

        yield 'Given value test should not be valid' => [
            'test',
            [MatchRule::PATTERN => '/^[0-9]+$/'],
            RuleInterface::ERROR,
            'value: test does not match /^[0-9]+$/',
        ];
    }

    #[DataProvider('getMatchValueProvider')]
    public function testValidate($value, $params, $expectedResult, $expectedError): void
    {
        $rule = new MatchRule('value', $value, $params);

        $res = $rule->validate();

        $this->assertSame($expectedResult, $res);

        $this->assertSame($expectedError, $rule->getError());
    }
}
