<?php

declare(strict_types = 1);

namespace Elie\Validator\Rule;

use PHPUnit\Framework\TestCase;

class MatchRuleTest extends TestCase
{

    /**
     * @dataProvider getMatchValueProvider
     */
    public function testValidate($value, $params, $expectedResult, $expectedError): void
    {
        $rule = new MatchRule('value', $value, $params);

        $res = $rule->validate();

        assertThat($res, identicalTo($expectedResult));

        assertThat($rule->getError(), identicalTo($expectedError));
    }

    public function getMatchValueProvider(): \Generator
    {
        yield 'Given value could be empty' => [
            '', ['pattern' => ''], RuleInterface::VALID, ''
        ];

        yield 'Given value test should be valid' => [
            'test', ['pattern' => '/^[a-z]+$/'], RuleInterface::VALID, ''
        ];

        yield 'Given value test should not be valid' => [
            'test', ['pattern' => '/^[0-9]+$/'], RuleInterface::ERROR, 'value: test does not match /^[0-9]+$/'
        ];
    }
}
