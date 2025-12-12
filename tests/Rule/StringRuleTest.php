<?php

declare(strict_types=1);

namespace Elie\Validator\Rule;

use Generator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class StringRuleTest extends TestCase
{

    public static function getStringValueProvider(): Generator
    {
        yield 'Given value could be empty' => [
            '',
            [],
            RuleInterface::VALID,
            '',
        ];

        yield 'Given value between 4 and 8 characters' => [
            'Peter ',
            [StringRule::MIN => 4, StringRule::MAX => 8],
            RuleInterface::VALID,
            '',
        ];

        yield 'Given value should not be cleaned' => [
            "f\x00f",
            [RuleInterface::REQUIRED => true],
            RuleInterface::VALID,
            '',
        ];

        yield 'Given value should be more than 3 characters' => [
            'Simon ',
            [StringRule::MIN => 3],
            RuleInterface::VALID,
            '',
        ];

        yield 'Given value should be a string not number' => [
            25,
            [],
            RuleInterface::ERROR,
            'name does not have a string value: 25',
        ];

        yield 'Given value should be a string not boolean' => [
            false,
            [],
            RuleInterface::ERROR,
            'name does not have a string value: <FALSE>',
        ];

        yield 'Given value is not between 4 and 8 characters' => [
            'Ben',
            [StringRule::MIN => 4, StringRule::MAX => 8],
            RuleInterface::ERROR,
            'name: The length of Ben is not between 4 and 8',
        ];
    }

    #[DataProvider('getStringValueProvider')]
    public function testValidate($value, $params, $expectedResult, $expectedError): void
    {
        $rule = new StringRule('name', $value, $params);

        $res = $rule->validate();

        $this->assertSame($expectedResult, $res);

        $this->assertSame($expectedError, $rule->getError());
    }
}
