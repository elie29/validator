<?php

declare(strict_types=1);

namespace Elie\Validator;

use Elie\Validator\Rule\ArrayRule;
use Elie\Validator\Rule\BooleanRule;
use Elie\Validator\Rule\CollectionRule;
use Elie\Validator\Rule\EmailRule;
use Elie\Validator\Rule\JsonRule;
use Elie\Validator\Rule\MatchRule;
use Elie\Validator\Rule\NumericRule;
use Elie\Validator\Rule\RuleInterface;
use Elie\Validator\Rule\StringRule;
use Elie\Validator\Stub\DataProvider as StubDataProvider;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\TestCase;

class ValidatorTest extends TestCase
{

    public function testValidatedContext(): void
    {
        $validator = new Validator(['name' => 'Ben ']);

        $rule = ['name', StringRule::class, 'min' => 3, 'max' => 30];

        $validator->setRules([$rule]);

        $res = $validator->validate();
        $this->assertTrue($res);

        $validatedContext = $validator->getValidatedContext();
        $this->assertArrayHasKey('name', $validatedContext);
        $this->assertContains('Ben', $validatedContext);

        $value = $validator->get('name');
        $this->assertSame('Ben ', $value);

        $value = $validator->get('age');
        $this->assertNull($value);

        $rules = $validator->getRules();
        $this->assertSame($rule, $rules[0]);

        $this->assertFalse($validator->shouldStopOnError());
    }

    public function testValidatorStopOnError(): void
    {
        $validator = new Validator(['name' => 'Ben '], [], true);

        $validator->setRules([
            ['name', StringRule::class, 'min' => 4, 'max' => 12],
        ]);

        $res = $validator->validate();
        $this->assertFalse($res);

        // value should not exist on error
        $validatedContext = $validator->getValidatedContext();
        $this->assertSame([], $validatedContext);

        $this->assertTrue($validator->shouldStopOnError());
    }

    public function testValidatorWithJsonPartialValidation(): void
    {
        $rules = [
            ['email', EmailRule::class, RuleInterface::REQUIRED => true],
            ['user', JsonRule::class, RuleInterface::REQUIRED => true, JsonRule::DECODE => true],
        ];

        $data = [
            'email' => 'elie29@gmail.com',
            'user' => '{"name": "John", "age": 25}',
        ];

        $validator = new Validator($data, $rules);

        $this->assertTrue($validator->validate());

        // In order to validate users json context
        $validator->setRules([
            ['name', MatchRule::class, MatchRule::PATTERN => '/^[a-z]{1,20}$/i'],
            ['age', NumericRule::class, NumericRule::MAX => 80],
        ]);

        $user = $validator->getValidatedContext()['user'];
        $validator->setContext($user);

        // Validate and merge context
        $this->assertTrue($validator->validate(true));

        $data = $validator->getValidatedContext();
        $this->assertSame(25, $data['age']);
        $this->assertArrayHasKey('user', $data);
    }

    public function testValidatorWithRawPartialValidation(): void
    {
        $rules = [
            ['email', EmailRule::class, RuleInterface::REQUIRED => true],
            ['tags', ArrayRule::class, RuleInterface::REQUIRED => true],
        ];

        $data = [
            'email' => 'elie29@gmail.com',
            'tags' => [
                ['code' => 12, 'slug' => 'one'],
                ['code' => 13, 'slug' => 'two'],
                ['code' => 15, 'slug' => 'three'],
            ],
        ];

        $validator = new Validator($data, $rules);

        $this->assertTrue($validator->validate());

        // In order to validate tags array context
        $validator->setRules([
            ['code', NumericRule::class, NumericRule::MAX => 80],
            ['slug', MatchRule::class, MatchRule::PATTERN => '/^[a-z]+$/i'],
        ]);

        $tags = $validator->getValidatedContext()['tags'];
        $data = [];
        foreach ($tags as $tag) {
            $validator->setContext($tag);
            $this->assertTrue($validator->validate());
            $data[] = $validator->getValidatedContext();
        }

        $this->assertSame(3, count($data));
    }

    public function testValidatorWithCollection(): void
    {
        $rules = [
            ['email', EmailRule::class, RuleInterface::REQUIRED => true],
            ['tags', CollectionRule::class, CollectionRule::RULES => [
                ['code', NumericRule::class, NumericRule::MAX => 80],
                ['slug', MatchRule::class, MatchRule::PATTERN => '/^[a-z]{1,5}$/i'],
            ]],
        ];

        $data = [
            'email' => 'elie29@gmail.com',
            'tags' => [
                ['code' => 12, 'slug' => 'one'],
                ['code' => 13, 'slug' => 'two'],
                ['code' => 15, 'slug' => 'three'],
            ],
        ];

        $validator = new Validator($data, $rules);

        $this->assertTrue($validator->validate());

        $tags = $validator->getValidatedContext()['tags'];

        $this->assertSame(3, count($tags));
    }

    public function testValidatorGetImplodedErrors(): void
    {
        $validator = new Validator(['name' => 'Ben'], [
            ['name', NumericRule::class],
            ['name', ArrayRule::class],
            ['name', BooleanRule::class],
        ]);

        $res = $validator->validate();
        $this->assertFalse($res);

        // value should not exist on error
        $validatedContext = $validator->getValidatedContext();
        $this->assertSame([], $validatedContext);

        $expected = 'name: Ben is not numeric,name does not have an array value: Ben,name: Ben is not a valid boolean';
        $this->assertSame($expected, $validator->getImplodedErrors(','));
    }

    public function testExistingKeysOnlyShouldBeAppendToTheValidatedContext(): void
    {
        $validator = new Validator(
        // Context
            ['name' => 'John', 'address' => null],
            // Rules
            [
                ['name', StringRule::class],
                ['address', StringRule::class],
                ['age', NumericRule::class],
            ]
        );

        $validator->appendExistingItemsOnly(true);

        $res = $validator->validate();
        $this->assertTrue($res);

        $validatedContext = $validator->getValidatedContext();

        $this->assertArrayHasKey('name', $validatedContext);
        $this->assertArrayHasKey('address', $validatedContext);
        $this->assertArrayNotHasKey('age', $validatedContext);
    }

    #[DataProviderExternal(StubDataProvider::class, 'getValidatorProvider')]
    public function testValidate($context, $rules, $expectedResult, $errorsSize): void
    {
        $validator = new Validator($context);
        $validator->setRules($rules);

        $res = $validator->validate();

        $this->assertSame($expectedResult, $res);
        $this->assertSame($errorsSize, count($validator->getErrors()));
    }
}
