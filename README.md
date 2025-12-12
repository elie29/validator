# Validator Project

[![build](https://github.com/elie29/validator/actions/workflows/php-build.yml/badge.svg)](https://github.com/elie29/validator/actions/workflows/php-build.yml)
[![Coverage Status](https://coveralls.io/repos/github/elie29/validator/badge.svg)](https://coveralls.io/github/elie29/validator)
![PHP Version](https://img.shields.io/badge/php-8.2%20|%208.3%20|%208.4%20|%208.5-blue)

## Introduction

A library for validating a context (POST, GET, etc...) by running given rules.

## Installation

Run the command below to install via Composer:

```shell
composer require elie29/validator
```

## Getting Started

`Validator` requires one or several rules ([constraints](#available-rules)) to validate a given context.

A basic example with \$\_POST

```php
<?php

use Elie\Validator\Rule\EmailRule;
use Elie\Validator\Rule\MultipleAndRule;
use Elie\Validator\Rule\NumericRule;
use Elie\Validator\Rule\RangeRule;
use Elie\Validator\Rule\StringRule;
use Elie\Validator\Validator;

/**
 * A key could have multiple rules
 *  - name could not be empty (required and minimum 1 character length)
 *  - age could be empty (non-existent, null or '') otherwise NumericRule is applied
 *  - age could be empty or among several values
 *  - email is required and should be a valid string email
 */
$rules =[
    ['name', StringRule::class, StringRule::MIN => 1, StringRule::REQUIRED => true],
    ['age', NumericRule::class, NumericRule::MAX => 60],
    ['age', RangeRule::class, RangeRule::RANGE => [30, 40, 50]],
    // Use composition instead of validating the key twice
    ['email', MultipleAndRule::class, MultipleAndRule::REQUIRED => true, MultipleAndRule::RULES => [
        [StringRule::class, StringRule::MAX => 255],
        [EmailRule::class],
    ]],
];

$validator = new Validator($_POST, $rules, true); // stop processing on error.

$validator->validate(); // bool depends on $_POST content
```

### Available rules

1. [All Rules](https://github.com/elie29/validator/tree/main/src/Rule/AbstractRule.php) accept `required`, `trim` and `messages` options.
   `required` is false by default while `trim` is true.
2. [ArrayRule](https://github.com/elie29/validator/tree/main/src/Rule/ArrayRule.php) accepts `min` and `max` options. Empty value is cast to an empty array [].
3. [BicRule](https://github.com/elie29/validator/tree/main/src/Rule/BicRule.php) validates Bank Identifier Code (SWIFT-BIC).
4. [BooleanRule](https://github.com/elie29/validator/tree/main/src/Rule/BooleanRule.php) accepts `cast` option.
5. [CallableRule](https://github.com/elie29/validator/tree/main/src/Rule/CallableRule.php) accepts `callable` function.
6. [ChoicesRule](https://github.com/elie29/validator/tree/main/src/Rule/ChoicesRule.php) accepts `list` option.
7. [CollectionRule](https://github.com/elie29/validator/tree/main/src/Rule/CollectionRule.php) accepts `rules` and `json` options.
8. [CompareRule](https://github.com/elie29/validator/tree/main/src/Rule/CompareRule.php) accepts `sign` and `expected` options. `sign` is [CompareRule::EQ](https://github.com/elie29/validator/tree/main/src/Rule/CompareConstants.php) by default, `expected` is null by default.
9. [DateRule](https://github.com/elie29/validator/tree/main/src/Rule/DateRule.php) accepts `format` and `separator` options.
10. [EmailRule](https://github.com/elie29/validator/tree/main/src/Rule/EmailRule.php) validates email addresses.
11. [IpRule](https://github.com/elie29/validator/tree/main/src/Rule/IpRule.php) accepts `flag` option.
12. [JsonRule](https://github.com/elie29/validator/tree/main/src/Rule/JsonRule.php) accepts `decode` option.
13. [MatchRule](https://github.com/elie29/validator/tree/main/src/Rule/MatchRule.php) requires `pattern` option.
14. [MultipleAndRule](https://github.com/elie29/validator/tree/main/src/Rule/MultipleAndRule.php) requires `rules` option (all rules must pass).
15. [MultipleOrRule](https://github.com/elie29/validator/tree/main/src/Rule/MultipleOrRule.php) requires `rules` option (at least one rule must pass).
16. [NumericRule](https://github.com/elie29/validator/tree/main/src/Rule/NumericRule.php) accepts `min`, `max` and `cast` options.
17. [RangeRule](https://github.com/elie29/validator/tree/main/src/Rule/RangeRule.php) accepts `range` option.
18. [StringCleanerRule](https://github.com/elie29/validator/tree/main/src/Rule/StringCleanerRule.php) removes invisible characters from strings.
19. [StringRule](https://github.com/elie29/validator/tree/main/src/Rule/StringRule.php) accepts `min` and `max` options.
20. [TimeRule](https://github.com/elie29/validator/tree/main/src/Rule/TimeRule.php) validates time format.
21. [Your own rule](#how-to-add-a-new-rule)

### How to add a new rule

You need to implement [RuleInterface](https://github.com/elie29/validator/tree/main/src/Rule/RuleInterface.php) or to extend [AbstractRule](https://github.com/elie29/validator/tree/main/src/Rule/AbstractRule.php)

```php
<?php

use Elie\Validator\Rule\AbstractRule;

class MyValueRule extends AbstractRule
{
    public const INVALID_MY_VALUE = 'invalidMyValue';

    protected mixed $my_value = null;

    public function __construct(int|string $key, mixed $value, array $params = [])
    {
        parent::__construct($key, $value, $params);

        if (isset($params['my_value'])) {
            $this->my_value = $params['my_value'];
        }

        // + to add a non-existent key
        $this->messages += [
            $this::INVALID_MY_VALUE => '%key%: %value% my message %my_value%'
        ];
    }

    public function validate(): int
    {
        $run = parent::validate();

        if ($run !== $this::CHECK) {
            return $run;
        }

        if ($this->value !== $this->my_value) {
            return $this->setAndReturnError($this::INVALID_MY_VALUE, [
                '%my_value%' => $this->stringify($this->my_value)
            ]);
        }

        return $this::VALID;
    }
}
```

## Validated Context

Once validate is called, we can use the validatedContext method to retrieve all validated values from the original
context.

By default, all keys set in the 'rules' array will be found in the validatedContext array. However, if we don't want to append
non-existing keys, we should call appendExistingItemsOnly(true) before validation.

## Assertion Integration

Instead of using assertion key by key, you can validate the whole context and then use [Assertion](https://github.com/beberlei/assert) or [Assert](https://github.com/webmozart/assert) as follows:

```php
<?php

use Assert\Assertion;
use Elie\Validator\Rule\EmailRule;
use Elie\Validator\Rule\NumericRule;
use Elie\Validator\Rule\RuleInterface;
use Elie\Validator\Rule\StringRule;
use Elie\Validator\Validator;
use Webmozart\Assert\Assert;

$rules =[
    ['age', NumericRule::class, NumericRule::MAX => 60],
    ['name', StringRule::class, StringRule::MIN => 1, StringRule::REQUIRED => true],
    ['email', EmailRule::class, EmailRule::REQUIRED => true],
];

$validator = new Validator($_POST, $rules);

// Using webmozart/assert
Assert::true($validator->validate(), $validator->getImplodedErrors());

// OR using beberlei/assert
Assertion::true($validator->validate(), $validator->getImplodedErrors());

// OR using PHPUnit in tests
$this->assertSame(RuleInterface::VALID, $validator->validate(), $validator->getImplodedErrors());
```

### Partial Validation

Sometimes we need to validate the context partially, whenever we have a JSON item or
keys that depend on each other.

The following is an example when a context - e.g., \$\_POST - should contain JSON user data:

```php
use Elie\Validator\Rule\JsonRule;
use Elie\Validator\Rule\MatchRule;
use Elie\Validator\Rule\NumericRule;
use Elie\Validator\Validator;

$rules = [
    ['user', JsonRule::class, JsonRule::REQUIRED => true],
];

$validator = new Validator($_POST, $rules);

Assertion::true($validator->validate()); // this assertion validates that the user is in JSON format

$validatedPost = $validator->getValidatedContext();

// But we need to validate user data as well (suppose it should contain name and age):

$rules = [
    ['name', MatchRule::class, MatchRule::PATTERN => '/^[a-z]{1,20}$/i'],
    ['age', NumericRule::class, NumericRule::MAX => 80],
];
$validator->setRules($rules);

// Decode user as it is a valid JSON
$user = json_decode($validatedPost['user'], true);
$validator->setContext($user); // the new context is now user data

Assertion::true($validator->validate()); // this assertion validates user data

/*
Validate accepts a boolean argument - mergedValidatedContext - which is false by default. If set to true, 
$validator->getValidatedContext() would return:

array:4 [▼
  "email" => "elie29@gmail.com"
  "user" => "{"name": "John", "age": 25}"
  "name" => "John"
  "age" => 25
]
*/
```

### Partial Validation with a multidimensional array

Usually with JsonRule, we could expect a multidimensional array. In this case, the validation process will be similar
to [Partial Validation](#partial-validation) without merging data:

```php
$rules = [
    // With json-decode, a validated value will be decoded into an array
    ['users', JsonRule::class, JsonRule::REQUIRED => true, JsonRule::DECODE => true],
];

$validator = new Validator([
    'users' => '[{"name":"John","age":25},{"name":"Brad","age":42}]'
], $rules);

Assertion::true($validator->validate()); // this validates that users is a valid JSON format

// But we need to validate all user data as well (suppose it should contain name and age):
$validator->setRules([
    ['name', MatchRule::class, MatchRule::PATTERN => '/^[a-z]{1,20}$/i'],
    ['age', NumericRule::class, NumericRule::MAX => 80],
]);

$validatedContext = $validator->getValidatedContext();

$users = $validatedContext['users'];

Assertion::isArray($users);

foreach ($users as $user) {
    // each user is a new context
    $validator->setContext($user);
    // do not merge data !!
    Assertion::true($validator->validate()); // we could validate all users and determine which ones are invalid!
}

```

A new [CollectionRule](https://github.com/elie29/validator/tree/main/src/Rule/CollectionRule.php) has been added to validate collection data (array or JSON) as follows:

```php
$rules = [
    ['users', CollectionRule::class, CollectionRule::JSON => true, CollectionRule::RULES => [
        ['name', MatchRule::class, MatchRule::PATTERN => '/^[a-z]{1,20}$/i'],
        ['age', NumericRule::class, NumericRule::MAX => 80],
    ]],
];

$data = [
    'users' => '[{"name":"John","age":25},{"name":"Brad","age":42}]'
];

$validator = new Validator($data, $rules);

$this->assertSame(RuleInterface::VALID, $validator->validate());

$users = $validator->getValidatedContext()['users'];

$this->assertCount(2, $users);
```

## Development Prerequisites

### Text file encoding

- UTF-8

### Composer commands

- `composer test`: Runs unit tests without coverage
- `composer test-coverage`: Runs unit tests with code coverage (requires Xdebug)
- `composer cover`: Runs tests with coverage and starts a local server to view coverage report at <http://localhost:5001>
- `composer clean`: Cleans all generated files (build directory and clover.xml)
