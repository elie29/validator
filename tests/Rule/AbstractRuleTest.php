<?php

declare(strict_types = 1);

namespace Elie\Validator\Rule;

use Elie\Validator\Rule\Stub\StubAbstractRule;
use PHPUnit\Framework\TestCase;

class AbstractRuleTest extends TestCase
{

    /**
     * @dataProvider getValueProvider
     */
    public function testValidateEmptyCases($value, $params, $expectedError, $expectedResult): void
    {
        /** @var RuleInterface $rule */
        $rule = new StubAbstractRule('key', $value, $params);

        $res = $rule->validate();
        assertThat($res, identicalTo($expectedResult));

        $error = $rule->getError();
        assertThat($error, identicalTo($expectedError));

        assertThat('key', identicalTo($rule->getKey()));
    }

    public function testValidatorValue(): void
    {
        /** @var RuleInterface $rule */
        $rule = new StubAbstractRule('key', ' ', []);

        $res = $rule->validate();
        assertThat($res, identicalTo(RuleInterface::VALID));

        assertThat('', equalTo($rule->getValue()));
    }

    public function getValueProvider(): \Generator
    {
        yield 'Trim is true but required is false by default' => [
            '  ',
            [],
            '',
            RuleInterface::VALID,
        ];

        yield 'Value could be empty if not required' => [
            '  ',
            [StringRule::TRIM => true, RuleInterface::REQUIRED => false],
            '',
            RuleInterface::VALID,
        ];

        yield 'Required value could be one character space' => [
            ' ',
            [StringRule::TRIM => false, RuleInterface::REQUIRED => true],
            '',
            RuleInterface::CHECK,
        ];

        yield 'Required value could be false' => [
            false,
            [RuleInterface::REQUIRED => true],
            '',
            RuleInterface::CHECK,
        ];

        yield 'Required value should not be an empty string' => [
            '',
            [RuleInterface::REQUIRED => true],
            'key is required and should not be empty: ',
            RuleInterface::ERROR,
        ];

        yield 'Required value should not be null' => [
            null,
            [RuleInterface::REQUIRED => true],
            'key is required and should not be empty: <NULL>',
            RuleInterface::ERROR,
        ];
    }
}
