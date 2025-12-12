<?php

declare(strict_types=1);

namespace Elie\Validator\Rule;

use Generator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class BooleanRuleTest extends TestCase
{

    public static function getBooleanValueProvider(): Generator
    {
        yield 'Given value could be empty' => [
            null,
            RuleInterface::VALID,
            '',
        ];

        yield 'Given value could be 1' => [
            '1 ',
            RuleInterface::VALID,
            '',
        ];

        yield 'Given value could be true' => [
            true,
            RuleInterface::VALID,
            '',
        ];

        yield 'Given value could not be a string' => [
            'test',
            RuleInterface::ERROR,
            'name: test is not a valid boolean',
        ];
    }

    public function testValidateEmptyValue(): void
    {
        // Value is not required by default!
        $rule = new BooleanRule('name', null);

        $res = $rule->validate();

        $this->assertSame(RuleInterface::VALID, $res);
        $this->assertNull($rule->getValue());

        $rule = new BooleanRule('name', '', [
            BooleanRule::CAST => true,
        ]);

        $res = $rule->validate();

        $this->assertSame(RuleInterface::VALID, $res);
        $this->assertFalse($rule->getValue());
    }

    #[DataProvider('getBooleanValueProvider')]
    public function testValidate($value, $expectedResult, $expectedError): void
    {
        $rule = new BooleanRule('name', $value);

        $res = $rule->validate();

        $this->assertSame($expectedResult, $res);

        $this->assertSame($expectedError, $rule->getError());
    }
}
