<?php

declare(strict_types=1);

namespace Elie\Validator\Rule;

use Generator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class JsonRuleTest extends TestCase
{

    public static function getJsonValueProvider(): Generator
    {
        yield 'Given value could be empty' => [
            '',
            RuleInterface::VALID,
            '',
        ];

        yield 'Given value {"a":1,"b":2,"c":3,"d":4,"e":5} should be valid' => [
            '{"a":1,"b":2,"c":3,"d":4,"e":5}',
            RuleInterface::VALID,
            '',
        ];

        yield 'Given value elie.com should not be valid' => [
            'elie.com',
            RuleInterface::ERROR,
            'json: elie.com is not a valid json format',
        ];
    }

    public function testValidateJsonDecode(): void
    {
        // Empty string value. Value is not required by default!
        $rule = new JsonRule('name', '');
        $res = $rule->validate();
        $this->assertSame(RuleInterface::VALID, $res);
        $this->assertSame('', $rule->getValue());

        $rule = new JsonRule('name', '', [JsonRule::DECODE => true]);
        $res = $rule->validate();
        $this->assertSame(RuleInterface::VALID, $res);
        $this->assertSame([], $rule->getValue());

        $rule = new JsonRule('name', '[{"name":"John Doe","age":25}]', [JsonRule::DECODE => true]);
        $res = $rule->validate();
        $this->assertSame(RuleInterface::VALID, $res);
        $this->assertSame(1, count($rule->getValue()));

        // With error
        $rule = new JsonRule('name', 'aaa', [JsonRule::DECODE => true]);
        $res = $rule->validate();
        $this->assertSame(RuleInterface::ERROR, $res);
        $this->assertSame('aaa', $rule->getValue());
    }

    #[DataProvider('getJsonValueProvider')]
    public function testValidate($value, $expectedResult, $expectedError): void
    {
        $rule = new JsonRule('json', $value);

        $res = $rule->validate();

        $this->assertSame($expectedResult, $res);

        $this->assertSame($expectedError, $rule->getError());
    }
}
