<?php

declare(strict_types = 1);

namespace Elie\Validator\Rule;

use PHPUnit\Framework\TestCase;
use Generator;

class ChoicesRuleTest extends TestCase
{

    /**
     * @dataProvider validDataProvider
     */
    public function testValidateEmptyValues($selection, $expectedResult): void
    {
        $list = ['foo', 'bar'];

        $rule = new ChoicesRule('key1', $selection, [ChoicesRule::LIST => $list]);

        assertThat($rule->validate(), is(equalTo(ChoicesRule::VALID)));
        assertThat($rule->getValue(), is(equalTo($expectedResult)));
    }

    public function validDataProvider(): Generator
    {
        yield 'Test null value' => [
            null,
            null,
        ];
        yield 'Test empty array value' => [
            [],
            [],
        ];
        yield 'Test foo value' => [
            ['foo'],
            ['foo'],
        ];
        yield 'Test array value' => [
            ['bar', 'foo'],
            ['bar', 'foo'],
        ];
    }

    public function testValidateError(): void
    {
        $list = ['foo', 'bar'];
        $selection = ['x'];

        $rule = new ChoicesRule('key2', $selection, [ChoicesRule::LIST => $list]);

        assertThat($rule->validate(), is(equalTo(ChoicesRule::ERROR)));
        assertThat($rule->getError(), is(stringValue()));
    }

    public function testValidateRequiredError(): void
    {
        $list = ['foo', 'bar'];
        $selection = [];

        $rule = new ChoicesRule('foo', $selection, [
            ChoicesRule::LIST => $list,
            ChoicesRule::REQUIRED => true
        ]);

        assertThat($rule->validate(), is(equalTo(ChoicesRule::ERROR)));
        assertThat($rule->getError(), is(stringValue()));
    }
}
