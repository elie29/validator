<?php

declare(strict_types = 1);

namespace Elie\Validator\Rule;

use PHPUnit\Framework\TestCase;

class JsonRuleTest extends TestCase
{

    /**
     * @dataProvider getJsonValueProvider
     */
    public function testValidate($value, $expectedResult, $expectedError): void
    {
        $rule = new JsonRule('json', $value);

        $res = $rule->validate();

        assertThat($res, identicalTo($expectedResult));

        assertThat($rule->getError(), identicalTo($expectedError));
    }

    public function getJsonValueProvider(): \Generator
    {
        yield 'Given value could be empty' => [
            '', RuleInterface::VALID, ''
        ];

        yield 'Given value {"a":1,"b":2,"c":3,"d":4,"e":5} should be valid' => [
            '{"a":1,"b":2,"c":3,"d":4,"e":5}', RuleInterface::VALID, ''
        ];

        yield 'Given value elie.com should not be valid' => [
            'elie.com', RuleInterface::ERROR, 'json: elie.com is not a valid json format'
        ];
    }
}
