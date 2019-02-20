<?php

declare(strict_types = 1);

namespace Elie\Validator\Rule;

use PHPUnit\Framework\TestCase;

class AbstractRuleTest extends TestCase
{

    protected function tearDown(): void
    {
        \Mockery::close();
        parent::tearDown();
    }

    /**
     * @dataProvider getValueProvider
     */
    public function testValidateEmptyCases($value, $params, $expectedError, $expectedResult): void
    {
        /*@var $rule RuleInterface */
        $rule = \Mockery::mock(AbstractRule::class, ['key', $value, $params])
            ->makePartial();

        $res = $rule->validate();
        assertThat($res, identicalTo($expectedResult));

        $error = $rule->getError();
        assertThat($error, identicalTo($expectedError));

        assertThat('key', identicalTo($rule->getKey()));
    }

    public function testValidatorValue(): void
    {
        /*@var $rule RuleInterface */
        $rule = \Mockery::mock(AbstractRule::class, ['key', ' '])
            ->makePartial();

        $res = $rule->validate();
        assertThat($res, identicalTo(RuleInterface::VALID));

        assertThat('', equalTo($rule->getValue()));
    }

    public function getValueProvider(): \Generator
    {
        yield 'Trim is true but required is false by default' => [
            '  ', [], '', RuleInterface::VALID
        ];

        yield 'Value could be empty if not required' => [
            '  ', ['trim' => true, 'required' => false], '', RuleInterface::VALID
        ];

        yield 'Required valued could be one character space' => [
            ' ', ['trim' => false, 'required' => true], '', RuleInterface::CHECK
        ];

        yield 'Required valued could be false' => [
            false, ['required' => true], '', RuleInterface::CHECK
        ];

        yield 'Required value should not be empty string' => [
            '', ['required' => true], 'key is required and should not be empty', RuleInterface::ERROR
        ];

        yield 'Required value should not be null' => [
            null, ['required' => true], 'key is required and should not be empty', RuleInterface::ERROR
        ];

        yield 'Required value should not be empty array' => [
            [], ['required' => true], 'key is required and should not be empty', RuleInterface::ERROR
        ];
    }
}
