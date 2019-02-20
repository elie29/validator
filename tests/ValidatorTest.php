<?php

declare(strict_types = 1);

namespace Elie\Validator;

use Elie\Validator\Rule\NumericRule;
use Elie\Validator\Rule\StringRule;
use PHPUnit\Framework\TestCase;

class ValidatorTest extends TestCase
{

    public function testValidatedContext(): void
    {
        $validator = new Validator(['name' => 'Ben ']);

        $rule = ['name', StringRule::class, 'min' => 3, 'max' => 30];

        $validator->setRules([$rule]);

        $res = $validator->validate();
        assertThat($res, is(true));

        $validatedContext = $validator->getValidatedContext();
        assertThat($validatedContext, anArray(['name' => 'Ben']));

        $value = $validator->get('name');
        assertThat($value, identicalTo('Ben '));

        $value = $validator->get('age');
        assertThat($value, nullValue());

        $rules = $validator->getRules();
        assertThat($rules[0], anInstanceOf(StringRule::class));

        assertThat($validator->shouldStopOnError(), is(false));
    }

    public function testValidatorStopOnError(): void
    {
        $validator = new Validator(['name' => 'Ben '], true);

        $validator->setRules([
            ['name', StringRule::class, 'min' => 4, 'max' => 12]
        ]);

        $res = $validator->validate();
        assertThat($res, is(false));

        // value should not exist on error
        $validatedContext = $validator->getValidatedContext();
        assertThat($validatedContext, emptyArray());

        assertThat($validator->shouldStopOnError(), is(true));
    }

    /**
     * @dataProvider getValidatorProvider
     */
    public function testValidate($context, $rules, $expectedResult, $errorsSize): void
    {
        $validator = new Validator($context);
        $validator->setRules($rules);

        $res = $validator->validate();

        assertThat($res, identicalTo($expectedResult));
        assertThat($validator->getErrors(), arrayWithSize($errorsSize));
    }

    public function getValidatorProvider(): \Generator
    {
        yield 'Age and name are valid' => [
            // context
            [
                'age' => 25,
                'name' => 'Ben'
            ],
            // rules
            [
                ['age', NumericRule::class, 'min' => 5, 'max' => 65],
                ['name', StringRule::class, 'min' => 3, 'max' => 30],
            ],
            // expectedResult
            true,
            // $errorsSize
            0
        ];

        yield 'Age is not valid' => [
            [
                'age' => 25,
            ],
            [
                ['age', NumericRule::class, 'min' => 26, 'max' => 65],
            ],
            false,
            1
        ];
    }
}
