<?php

declare(strict_types=1);

namespace Elie\Validator\Rule;

use Elie\Validator\Stub\DataProvider as StubDataProvider;
use Elie\Validator\Stub\StubAbstractRule;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\TestCase;

class AbstractRuleTest extends TestCase
{

    #[DataProviderExternal(StubDataProvider::class, 'getValueProvider')]
    public function testValidateEmptyCases($value, $params, $expectedResult, $expectedError): void
    {
        /** @var RuleInterface $rule */
        $rule = new StubAbstractRule('key', $value, $params);

        $res = $rule->validate();
        $this->assertSame($expectedResult, $res);

        $error = $rule->getError();
        $this->assertSame($expectedError, $error);

        $this->assertSame('key', $rule->getKey());
    }

    public function testValidatorValue(): void
    {
        /** @var RuleInterface $rule */
        $rule = new StubAbstractRule('key', ' ', []);

        $res = $rule->validate();
        $this->assertSame(RuleInterface::VALID, $res);

        $this->assertSame('', $rule->getValue());
    }
}
