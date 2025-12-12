<?php

declare(strict_types=1);

namespace Elie\Validator\Rule;

use Elie\Validator\Stub\DataProvider as StubDataProvider;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\TestCase;

class ArrayRuleTest extends TestCase
{

    public function testValidateEmptyValue(): void
    {
        // Empty string value. Value is not required by default!
        $rule = new ArrayRule('name', []);
        $res = $rule->validate();
        $this->assertSame(RuleInterface::VALID, $res);
        $this->assertSame([], $rule->getValue());

        $rule = new ArrayRule('name', ['foo' => 'bar']);
        $res = $rule->validate();
        $this->assertSame(RuleInterface::VALID, $res);
        $this->assertSame('bar', $rule->getValue()['foo']);

        // with error: value remains unchanged
        $rule = new ArrayRule('name', 15);
        $res = $rule->validate();
        $this->assertSame(RuleInterface::ERROR, $res);
        $this->assertSame(15, $rule->getValue());
    }

    #[DataProviderExternal(StubDataProvider::class, 'getArrayValueProvider')]
    public function testValidate($value, $params, $expectedResult, $expectedError): void
    {
        $rule = new ArrayRule('name', $value, $params);

        $res = $rule->validate();

        $this->assertSame($expectedResult, $res);

        $this->assertSame($expectedError, $rule->getError());
    }
}

