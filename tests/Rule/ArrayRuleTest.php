<?php

declare(strict_types = 1);

namespace Elie\Validator\Rule;

use PHPUnit\Framework\TestCase;

class ArrayRuleTest extends TestCase
{

    /**
     * @dataProvider getArrayValueProvider
     */
    public function testValidate($value, $params, $expectedResult, $expectedError): void
    {
        $rule = new ArrayRule('name', $value, $params);

        $res = $rule->validate();

        assertThat($res, identicalTo($expectedResult));

        assertThat($rule->getError(), identicalTo($expectedError));
    }

    public function getArrayValueProvider(): \Generator
    {
        yield 'Given value could be empty' => [
            '', [], RuleInterface::VALID, ''
        ];

        yield 'Given value between 4 and 8' => [
            ['Peter', 'Ben', 'Harold'], [RuleInterface::MIN => 3, RuleInterface::MAX => 8], RuleInterface::VALID, ''
        ];

        yield 'Given value should be more than 3' => [
            ['Peter', 'Ben', 'Harold'], [RuleInterface::MIN => 3], RuleInterface::VALID, ''
        ];

        yield 'Given value should be an array' => [
            new \stdClass(), [], RuleInterface::ERROR, 'name does not have an array value: stdClass object'
        ];

        yield 'Given value is not between 4 and 8' => [
            ['Peter', 'Ben', 'Harold'], [RuleInterface::MIN => 4, RuleInterface::MAX => 8], RuleInterface::ERROR,
            "name: The length of array (  0 => 'Peter',  1 => 'Ben',  2 => 'Harold',) is not between 4 and 8"
        ];
    }
}
