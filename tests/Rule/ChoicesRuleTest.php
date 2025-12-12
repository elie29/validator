<?php

declare(strict_types=1);

namespace Elie\Validator\Rule;

use Generator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ChoicesRuleTest extends TestCase
{

    public static function validDataProvider(): Generator
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

    #[DataProvider('validDataProvider')]
    public function testValidateEmptyValues($selection, $expectedResult): void
    {
        $list = ['foo', 'bar'];

        $rule = new ChoicesRule('key1', $selection, [ChoicesRule::LIST => $list]);

        $this->assertSame(RuleInterface::VALID, $rule->validate());
        $this->assertSame($expectedResult, $rule->getValue());
    }

    public function testValidateError(): void
    {
        $list = ['foo', 'bar'];
        $selection = ['x'];

        $rule = new ChoicesRule('key2', $selection, [ChoicesRule::LIST => $list]);

        $this->assertSame(RuleInterface::ERROR, $rule->validate());
        $this->assertIsString($rule->getError());
    }

    public function testValidateRequiredError(): void
    {
        $list = ['foo', 'bar'];
        $selection = [];

        $rule = new ChoicesRule('foo', $selection, [
            ChoicesRule::LIST => $list,
            RuleInterface::REQUIRED => true,
        ]);

        $this->assertSame(RuleInterface::ERROR, $rule->validate());
        $this->assertIsString($rule->getError());
    }
}
