<?php

declare(strict_types = 1);

namespace Elie\Validator\Rule;

use PHPUnit\Framework\TestCase;

class EmailRuleTest extends TestCase
{

    /**
     * @dataProvider getEmailValueProvider
     */
    public function testValidate($value, $expectedResult, $expectedError): void
    {
        $rule = new EmailRule('email', $value);

        $res = $rule->validate();

        assertThat($res, identicalTo($expectedResult));

        assertThat($rule->getError(), identicalTo($expectedError));
    }

    public function getEmailValueProvider(): \Generator
    {
        yield 'Given value could be empty' => [
            '', RuleInterface::VALID, ''
        ];

        yield 'Given value elie29@gmail.com should be valid' => [
            'elie29@gmail.com', RuleInterface::VALID, ''
        ];

        yield 'Given value elie.com should not be valid' => [
            'elie.com', RuleInterface::ERROR, 'email: elie.com is not a valid email'
        ];
    }
}
