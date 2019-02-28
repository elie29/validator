<?php

declare(strict_types = 1);

namespace Elie\Validator\Rule;

use PHPUnit\Framework\TestCase;

class StringCleanerRuleTest extends TestCase
{

    /**
     * @dataProvider getStringValueProvider
     */
    public function testValidate($value, $params, $expectedValue, $expectedError): void
    {
        $rule = new StringCleanerRule('name', $value, $params);

        $rule->validate();

        assertThat($rule->getValue(), identicalTo($expectedValue));

        assertThat($rule->getError(), identicalTo($expectedError));
    }

    public function getStringValueProvider(): \Generator
    {
        yield 'Given value is cleaned by trim' => [
            "\x00", [], '', ''
        ];

        yield 'Given value should be cleaned' => [
            "f\x00f", [StringCleanerRule::REQUIRED => true], 'ff', ''
        ];

        yield 'Given value between 4 and 8 characters' => [
            '%7FPeter ', [StringCleanerRule::MIN => 4, StringCleanerRule::MAX => 8], 'Peter', ''
        ];
    }
}
