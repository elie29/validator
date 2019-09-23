<?php

declare(strict_types = 1);

namespace Elie\Validator\Rule;

use PHPUnit\Framework\TestCase;

class MultipleAndRuleTest extends TestCase
{

    public function testValidate(): void
    {
        // Should be string and email
        $rule = new MultipleAndRule('name', 'foo@gmail.com', [
            MultipleAndRule::RULES => [
                [StringRule::class, StringRule::REQUIRED => true, StringRule::MIN => 1],
                [EmailRule::class],
            ]
        ]);

        $res = $rule->validate();

        assertThat($res, identicalTo(RuleInterface::VALID));

        assertThat($rule->getError(), is(emptyString()));
    }

    public function testValidateCouldBeEmpry(): void
    {
        // Could be empty or a valid email string
        $rule = new MultipleAndRule('name', '', [
            MultipleAndRule::RULES => [
                [StringRule::class, StringRule::REQUIRED => true, StringRule::MIN => 1],
                [EmailRule::class],
            ]
        ]);

        $res = $rule->validate();

        assertThat($res, identicalTo(RuleInterface::VALID));

        assertThat($rule->getError(), is(emptyString()));
    }

    public function testValidateError(): void
    {
        // Should be string and email
        $rule = new MultipleAndRule('name', 'foo', [
            MultipleAndRule::RULES => [
                [StringRule::class, StringRule::REQUIRED => true, StringRule::MIN => 1],
                [EmailRule::class],
            ]
        ]);

        $res = $rule->validate();

        assertThat($res, identicalTo(RuleInterface::ERROR));

        assertThat($rule->getError(), is(identicalTo('name: foo is not a valid email')));
    }
}
