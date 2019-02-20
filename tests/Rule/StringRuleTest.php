<?php

declare(strict_types = 1);

namespace Elie\Validator\Rule;

use PHPUnit\Framework\TestCase;

class StringRuleTest extends TestCase
{

    /**
     * @dataProvider getStringValueProvider
     */
    public function testValidate($value, $params, $expectedResult, $expectedError): void
    {
        $rule = new StringRule('name', $value, $params);

        $res = $rule->validate();

        assertThat($res, identicalTo($expectedResult));

        assertThat($rule->getError(), identicalTo($expectedError));
    }

    public function getStringValueProvider(): \Generator
    {
        yield 'Given value could be empty' => [
            '', [], RuleInterface::VALID, ''
        ];

        yield 'Given value between 4 and 8 characters' => [
            'Peter ', ['min' => 4, 'max' => 8], RuleInterface::VALID, ''
        ];

        yield 'Given value should be more than 3 characters' => [
            'Simon ', ['min' => 3], RuleInterface::VALID, ''
        ];

        yield 'Given value should be a string' => [
            25, [], RuleInterface::ERROR, 'name does not have a string value'
        ];

        yield 'Given value is not between 4 and 8 characters' => [
            'Ben', ['min' => 4, 'max' => 8], RuleInterface::ERROR, 'name: The length of Ben is not between 4 and 8'
        ];
    }
}
