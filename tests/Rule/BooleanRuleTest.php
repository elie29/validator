<?php

declare(strict_types = 1);

namespace Elie\Validator\Rule;

use PHPUnit\Framework\TestCase;

class BooleanRuleTest extends TestCase
{

    public function testValidateEmptyValue(): void
    {
        // Empty string value.  Value is not required by default!
        $rule = new BooleanRule('name', '');

        $res = $rule->validate();

        assertThat($res, identicalTo(BooleanRule::VALID));
        assertThat($rule->getValue(), emptyString());

        $rule = new BooleanRule('name', '', [
            BooleanRule::CAST => true
        ]);

        $res = $rule->validate();

        assertThat($res, identicalTo(BooleanRule::VALID));
        assertThat($rule->getValue(), identicalTo(false));
    }

    /**
     * @dataProvider getBooleanValueProvider
     */
    public function testValidate($value, $expectedResult, $expectedError): void
    {
        $rule = new BooleanRule('name', $value);

        $res = $rule->validate();

        assertThat($res, identicalTo($expectedResult));

        assertThat($rule->getError(), identicalTo($expectedError));
    }

    public function getBooleanValueProvider(): \Generator
    {
        yield 'Given value could be empty' => [
            '', BooleanRule::VALID, ''
        ];

        yield 'Given value could be 1' => [
            '1 ', BooleanRule::VALID, ''
        ];

        yield 'Given value could be true' => [
            true, BooleanRule::VALID, ''
        ];

        yield 'Given value could not be a string' => [
            'test', BooleanRule::ERROR, 'name: test is not a valid boolean'
        ];
    }
}
