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
            '  ', [RuleInterface::TRIM => true, RuleInterface::REQUIRED => false], '', RuleInterface::VALID
        ];

        yield 'Required value could be one character space' => [
            ' ', [RuleInterface::TRIM => false, RuleInterface::REQUIRED => true], '', RuleInterface::CHECK
        ];

        yield 'Required value could be false' => [
            false, [RuleInterface::REQUIRED => true], '', RuleInterface::CHECK
        ];

        yield 'Required value should not be an empty string' => [
            '', [RuleInterface::REQUIRED => true], 'key is required and should not be empty: ', RuleInterface::ERROR
        ];

        yield 'Required value should not be null' => [
            null, [RuleInterface::REQUIRED => true], 'key is required and should not be empty: ', RuleInterface::ERROR
        ];

        yield 'Required value should not be an empty array' => [
            [], [RuleInterface::REQUIRED => true], 'key is required and should not be empty: array ()',
            RuleInterface::ERROR
        ];

        yield 'Required value should not be an empty array, with specific message' => [
            [], [RuleInterface::REQUIRED => true, 'messages' => [
                RuleInterface::EMPTY_KEY => '%key% is required. `%value%` is empty!'
            ]],
            'key is required. `array ()` is empty!', RuleInterface::ERROR
        ];
    }
}
