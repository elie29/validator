<?php

declare(strict_types = 1);

namespace Elie\Validator\Rule;

use PHPUnit\Framework\TestCase;

class MultipleOrRuleTest extends TestCase
{

    public function testValidate(): void
    {
        // Should be string and numeric
        $rule = new MultipleOrRule('name', '562', [
            MultipleAndRule::RULES => [
                [StringRule::class, StringRule::REQUIRED => true, StringRule::MAX => 2],
                [NumericRule::class],
            ]
        ]);

        $res = $rule->validate();

        assertThat($res, identicalTo(RuleInterface::VALID));

        assertThat($rule->getError(), is(emptyString()));
    }

    public function testValidateErrors(): void
    {
        $rule = new MultipleOrRule('name', 'abc', [
            MultipleAndRule::RULES => [
                [StringRule::class, StringRule::REQUIRED => true, StringRule::MAX => 2],
                [NumericRule::class],
            ]
        ]);

        $res = $rule->validate();

        assertThat($res, identicalTo(RuleInterface::ERROR));

        assertThat($rule->getError(), equalTo(
            "name: The length of abc is not between 0 and 2\nname: abc is not numeric"
        ));
    }
}
