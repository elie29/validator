<?php

declare(strict_types=1);

namespace Elie\Validator\Rule;

use PHPUnit\Framework\TestCase;

class CollectionRuleTest extends TestCase
{

    public function testArrayValidate(): void
    {
        $rules = [
            ['code', NumericRule::class, NumericRule::MAX => 80],
            ['slug', MatchRule::class, MatchRule::PATTERN => '/^[a-z]{1,5}$/i'],
        ];

        $data = [
            ['code' => 12, 'slug' => 'one'],
            ['code' => 13, 'slug' => 'two'],
            ['code' => 15, 'slug' => 'three'],
        ];

        $rule = new CollectionRule('tags', $data, [CollectionRule::RULES => $rules]);

        $this->assertEquals(RuleInterface::VALID, $rule->validate());

        $tags = $rule->getValue();

        $this->assertSame(3, count($tags));
        $this->assertSame('one', $tags[0]['slug']);
        $this->assertSame($data, $tags);
    }

    public function testJsonValidate(): void
    {
        $rules = [
            ['code', NumericRule::class, NumericRule::MAX => 80],
            ['slug', MatchRule::class, MatchRule::PATTERN => '/^[a-z]{1,5}$/i'],
        ];

        $data = json_encode([
            ['code' => 12, 'slug' => 'one'],
            ['code' => 13, 'slug' => 'two'],
            ['code' => 15, 'slug' => 'three'],
        ]);

        $rule = new CollectionRule('tags', $data, [CollectionRule::RULES => $rules, CollectionRule::JSON => true]);

        $this->assertEquals(RuleInterface::VALID, $rule->validate());

        $tags = $rule->getValue();

        $this->assertSame(3, count($tags));
    }

    public function testValidateEmptyData(): void
    {
        $rules = [
            ['code', NumericRule::class, NumericRule::MAX => 80],
        ];

        $data = null;

        $rule = new CollectionRule('tags', $data, [CollectionRule::RULES => $rules]);

        $this->assertEquals(RuleInterface::VALID, $rule->validate());

        $this->assertTrue(is_array($rule->getValue())); // value cast to array
    }

    public function testValidateOnError(): void
    {
        $rules = [
            ['code', NumericRule::class, NumericRule::MAX => 80],
            ['slug', MatchRule::class, MatchRule::PATTERN => '/^[a-z]{1,3}$/i'],
        ];

        $data = [
            ['code' => 12, 'slug' => 'one'],
            ['code' => 13, 'slug' => 'two'],
            ['code' => 15, 'slug' => 'three'],
        ];

        $rule = new CollectionRule('tags', $data, [CollectionRule::RULES => $rules]);

        $this->assertEquals(RuleInterface::ERROR, $rule->validate());

        $this->assertStringContainsString('slug: three does not match /^[a-z]{1,3}$/i', $rule->getError());
    }

    public function testValidateErrorFormat(): void
    {
        $data = '{email: "elie29@gmail.com"}';
        $rules = [
            ['email', EmailRule::class],
        ];

        $rule = new CollectionRule('tags', $data, [CollectionRule::RULES => $rules]);

        $this->assertEquals(RuleInterface::ERROR, $rule->validate());
        $this->assertStringContainsString('tags: {email: "elie29@gmail.com"} is not in a collection', $rule->getError());
    }

    public function testValidateWithEmptyRules(): void
    {
        $data = [
            ['code' => 12, 'slug' => 'one'],
            ['code' => 13, 'slug' => 'two'],
        ];

        // Empty rules should make the rule valid
        $rule = new CollectionRule('tags', $data, []);

        $this->assertEquals(RuleInterface::VALID, $rule->validate());
    }

    public function testValidateMalformedJson(): void
    {
        $data = '{"invalid": json without closing brace';
        $rules = [
            ['code', NumericRule::class],
        ];

        $rule = new CollectionRule('tags', $data, [CollectionRule::RULES => $rules, CollectionRule::JSON => true]);

        $this->assertEquals(RuleInterface::ERROR, $rule->validate());
        $this->assertStringContainsString('is not in a collection', $rule->getError());
    }

    public function testValidateNonJsonStringWithJsonFlag(): void
    {
        $data = 'plain text not json';
        $rules = [
            ['code', NumericRule::class],
        ];

        $rule = new CollectionRule('tags', $data, [CollectionRule::RULES => $rules, CollectionRule::JSON => true]);

        $this->assertEquals(RuleInterface::ERROR, $rule->validate());
        $this->assertStringContainsString('is not in a collection', $rule->getError());
    }

    public function testGetValueReturnsEmptyArrayWhenValueIsNullAndNoError(): void
    {
        $rule = new CollectionRule('tags', null, []);

        $rule->validate();

        $this->assertSame([], $rule->getValue());
    }
}
