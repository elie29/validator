<?php

declare(strict_types=1);

namespace Elie\Validator\Rule;

use PHPUnit\Framework\TestCase;

class MultipleOrRuleTest extends TestCase
{

    public function testValidate(): void
    {
        // Should be string and numeric
        $rule = new MultipleOrRule('name', '562', [
            MultipleAndRule::RULES => [
                [StringRule::class, RuleInterface::REQUIRED => true, StringRule::MAX => 2],
                [NumericRule::class],
            ],
        ]);

        $res = $rule->validate();

        $this->assertSame(RuleInterface::VALID, $res);

        $this->assertSame('', $rule->getError());
    }

    public function testValidateErrors(): void
    {
        $rule = new MultipleOrRule('name', 'abc', [
            MultipleAndRule::RULES => [
                [StringRule::class, RuleInterface::REQUIRED => true, StringRule::MAX => 2],
                [NumericRule::class],
            ],
        ]);

        $res = $rule->validate();

        $this->assertSame(RuleInterface::ERROR, $res);

        $this->assertSame(
            "name: The length of abc is not between 0 and 2\nname: abc is not numeric",
            $rule->getError()
        );
    }

    public function testValidateWithFirstRulePassing(): void
    {
        // First rule passes, should be valid immediately
        $rule = new MultipleOrRule('value', '15', [
            MultipleAndRule::RULES => [
                [NumericRule::class],
                [EmailRule::class], // Won't be checked since first rule passes
            ],
        ]);

        $res = $rule->validate();

        $this->assertSame(RuleInterface::VALID, $res);
        $this->assertSame('', $rule->getError());
    }

    public function testValidateWithSecondRulePassing(): void
    {
        // First rule fails, second rule passes
        $rule = new MultipleOrRule('contact', 'user@example.com', [
            MultipleAndRule::RULES => [
                [NumericRule::class], // Fails
                [EmailRule::class], // Passes
            ],
        ]);

        $res = $rule->validate();

        $this->assertSame(RuleInterface::VALID, $res);
        $this->assertSame('', $rule->getError());
    }

    public function testValidateWithEmptyValue(): void
    {
        // Empty value should be valid (not required)
        $rule = new MultipleOrRule('value', '', [
            MultipleAndRule::RULES => [
                [NumericRule::class],
                [EmailRule::class],
            ],
        ]);

        $res = $rule->validate();

        $this->assertSame(RuleInterface::VALID, $res);
        $this->assertSame('', $rule->getError());
    }

    public function testValidateMultipleErrorsAccumulated(): void
    {
        // All rules fail, should accumulate all errors
        $rule = new MultipleOrRule('data', 'invalid', [
            MultipleAndRule::RULES => [
                [NumericRule::class],
                [EmailRule::class],
                [BooleanRule::class],
            ],
        ]);

        $res = $rule->validate();

        $this->assertSame(RuleInterface::ERROR, $res);
        
        $error = $rule->getError();
        $this->assertStringContainsString('is not numeric', $error);
        $this->assertStringContainsString('is not a valid email', $error);
        $this->assertStringContainsString('is not a valid boolean', $error);
    }
}
