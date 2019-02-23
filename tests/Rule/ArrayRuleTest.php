<?php

declare(strict_types = 1);

namespace Elie\Validator\Rule;

use PHPUnit\Framework\TestCase;

class ArrayRuleTest extends TestCase
{

    public function testValidateEmptyValue(): void
    {
        // Empty string value. Value is not required by default!
        $rule = new ArrayRule('name', '');
        $res = $rule->validate();
        assertThat($res, identicalTo(ArrayRule::VALID));
        assertThat($rule->getValue(), emptyArray());

        $rule = new ArrayRule('name', ['foo' => 'bar']);
        $res = $rule->validate();
        assertThat($res, identicalTo(ArrayRule::VALID));
        assertThat($rule->getValue(), hasEntry('foo', 'bar'));

        // with error : value remains unchanged
        $rule = new ArrayRule('name', 15);
        $res = $rule->validate();
        assertThat($res, identicalTo(ArrayRule::ERROR));
        assertThat($rule->getValue(), is(15));
    }

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
            '', [], ArrayRule::VALID, ''
        ];

        yield 'Given value between 4 and 8' => [
            ['Peter', 'Ben', 'Harold'], [ArrayRule::MIN => 3, ArrayRule::MAX => 8], ArrayRule::VALID, ''
        ];

        yield 'Given value should be more than 3' => [
            ['Peter', 'Ben', 'Harold'], [ArrayRule::MIN => 3], ArrayRule::VALID, ''
        ];

        yield 'Given value should be an array' => [
            new \stdClass(), [], ArrayRule::ERROR, 'name does not have an array value: stdClass object'
        ];

        yield 'Given value is not between 4 and 8' => [
            ['Peter', 'Ben', 'Harold'], [ArrayRule::MIN => 4, ArrayRule::MAX => 8], ArrayRule::ERROR,
            "name: The length of array (  0 => 'Peter',  1 => 'Ben',  2 => 'Harold',) is not between 4 and 8"
        ];
    }
}
