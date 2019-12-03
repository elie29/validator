<?php

declare(strict_types = 1);

namespace Elie\Validator\Rule;

use PHPUnit\Framework\TestCase;

class CallableRuleTest extends TestCase
{

    /**
     * @dataProvider getCallableRuleValueProvider
     */
    public function testValidate($value, array $params, int $expectedResult, string $expectedError): void
    {
        $rule = new CallableRule('name', $value, $params);

        $res = $rule->validate();

        assertThat($res, identicalTo($expectedResult));

        assertThat($rule->getError(), identicalTo($expectedError));
    }

    public function getCallableRuleValueProvider(): \Generator
    {
        yield 'Given value could be empty when not required' => [
            null, [CallableRule::CALLABLE => function ($key, $value): bool {
                return $value === '102';
            }], CallableRule::VALID, ''
        ];

        yield 'Given value should be validated through a callable' => [
            '102', [CallableRule::CALLABLE => function ($key, $value, CallableRule $rule): bool {
                if ($value === '102') {
                    $rule->setValue(102);
                    return true;
                }
                return false;
            }], CallableRule::VALID, ''
        ];

        yield 'Given value should not be validated through a callable' => [
            false, [CallableRule::CALLABLE => function ($key, $value): bool {
                return $value === null;
            }], CallableRule::ERROR, 'name: <FALSE> did not pass the callable check'
        ];
    }
}
