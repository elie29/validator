<?php

declare(strict_types=1);

namespace Elie\Validator\Rule;

use PHPUnit\Framework\TestCase;

class MultipleAndRuleTest extends TestCase
{

    public function testValidate(): void
    {
        // Should be string and email
        $rule = new MultipleAndRule('name', 'foo@gmail.com', [
            MultipleAndRule::RULES => [
                [StringRule::class, RuleInterface::REQUIRED => true, StringRule::MIN => 1],
                [EmailRule::class],
            ],
        ]);

        $res = $rule->validate();

        $this->assertSame(RuleInterface::VALID, $res);

        $this->assertSame('', $rule->getError());
    }

    public function testValidateCouldBeEmpty(): void
    {
        // Could be empty or a valid email string
        $rule = new MultipleAndRule('name', '', [
            MultipleAndRule::RULES => [
                [StringRule::class, RuleInterface::REQUIRED => true, StringRule::MIN => 1],
                [EmailRule::class],
            ],
        ]);

        $res = $rule->validate();

        $this->assertSame(RuleInterface::VALID, $res);

        $this->assertSame('', $rule->getError());
    }

    public function testValidateError(): void
    {
        // Should be string and email
        $rule = new MultipleAndRule('name', 'foo', [
            MultipleAndRule::RULES => [
                [StringRule::class, RuleInterface::REQUIRED => true, StringRule::MIN => 1],
                [EmailRule::class],
            ],
        ]);

        $res = $rule->validate();

        $this->assertSame(RuleInterface::ERROR, $res);

        $this->assertSame('name: foo is not a valid email', $rule->getError());
    }

    public function testValidateWithNumericAndRangeRules(): void
    {
        // Value must be numeric and within range
        $rule = new MultipleAndRule('score', '75', [
            MultipleAndRule::RULES => [
                [NumericRule::class, RuleInterface::REQUIRED => true],
                [RangeRule::class, RangeRule::RANGE => ['50', '75']],
            ],
        ]);

        $res = $rule->validate();

        $this->assertSame(RuleInterface::VALID, $res);
        $this->assertSame('', $rule->getError());
    }

    public function testValidateMultipleRulesWithFirstFailure(): void
    {
        // When the first rule fails, validation should stop at the first error
        $rule = new MultipleAndRule('value', 'abc', [
            MultipleAndRule::RULES => [
                [NumericRule::class, RuleInterface::REQUIRED => true],
                [RangeRule::class, RangeRule::RANGE => [50, 75]],
            ],
        ]);

        $res = $rule->validate();

        $this->assertSame(RuleInterface::ERROR, $res);
        $this->assertStringContainsString('is not numeric', $rule->getError());
    }

    public function testValidateWithThreeRules(): void
    {
        // Test with three rules all passing
        $rule = new MultipleAndRule('email', 'test@example.com', [
            MultipleAndRule::RULES => [
                [StringRule::class, RuleInterface::REQUIRED => true, StringRule::MIN => 5],
                [StringRule::class, StringRule::MAX => 50],
                [EmailRule::class],
            ],
        ]);

        $res = $rule->validate();

        $this->assertSame(RuleInterface::VALID, $res);
        $this->assertSame('', $rule->getError());
    }
}
