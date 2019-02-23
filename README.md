# Validator Project

[![Build Status](https://travis-ci.org/elie29/validator.svg?branch=master)](https://travis-ci.org/elie29/validator)
[![Coverage Status](https://coveralls.io/repos/github/elie29/validator/badge.svg)](https://coveralls.io/github/elie29/validator)

## Introduction
A library for validating a context (POST, GET etc...) by running given rules.

## Installation ##

Run the command below to install via Composer:

```shell
composer require elie29/validator
```

## Getting Started ##
`Validator` requires one or several rules ([constraints](#available-rules)) in order to validate a given context.

A basic example with $_POST
```php
<?php

use Elie\Validator\Rule\EmailRule;
use Elie\Validator\Rule\NumericRule;
use Elie\Validator\Rule\StringRule;
use Elie\Validator\Validator;

/**
 * A key could have multiple rules
 *  - age could be empty (unexistant, null or '') otherwise NumericRule is applied
 *  - name could not be empty (required and minimum 1 character length)
 *  - email is required and should be a valid email
 */
$rules =[
    ['age', NumericRule::class, NumericRule::MAX => 60],
    ['name', StringRule::class, StringRule::MIN => 1, StringRule::REQUIRED => true],
    ['email', EmailRule::class, EmailRule::REQUIRED => true],
];

$validator = new Validator($_POST, $rules, true); // stop processing on error.

$validator->validate(); // bool depends on $_POST content
```

### Available rules ###

1. [All Rulles](https://github.com/elie29/validator/blob/master/src/Rule/AbstractRule.php) accept `required`, `trim` and `messages` options.
 `required` is false by default while `trim` is true.
1. [BicRule](https://github.com/elie29/validator/blob/master/src/Rule/BicRule.php)
1. [BooleanRule](https://github.com/elie29/validator/blob/master/src/Rule/BooleanRule.php)
1. [CompareRule](https://github.com/elie29/validator/blob/master/src/Rule/CompareRule.php) accepts `sign` and `expected` options. `sign` is [CompareRule::EQ](https://github.com/elie29/validator/blob/master/src/Rule/CompareConstants.php) by default, `expected` is null by default.
1. [DateRule](https://github.com/elie29/validator/blob/master/src/Rule/DateRule.php) accepts `format` and `separator` options.
1. [EmailRule](https://github.com/elie29/validator/blob/master/src/Rule/EmailRule.php)
1. [IpRule](https://github.com/elie29/validator/blob/master/src/Rule/IpRule.php) accepts `flag` option.
1. [JsonRule](https://github.com/elie29/validator/blob/master/src/Rule/JsonRule.php)
1. [MatchRule](https://github.com/elie29/validator/blob/master/src/Rule/MatchRule.php) requires `pattern` option.
1. [NumericRule](https://github.com/elie29/validator/blob/master/src/Rule/NumericRule.php) accepts `min` and `max` options.
1. [RangeRule](https://github.com/elie29/validator/blob/master/src/Rule/RangeRule.php) accepts `range` option.
1. [StringRule](https://github.com/elie29/validator/blob/master/src/Rule/StringRule.php) accepts `min` and `max` options.
1. [TimeRule](https://github.com/elie29/validator/blob/master/src/Rule/TimeRule.php)
1. [Your own rule](#how-to-add-a-new-rule)

### How to add a new rule ###

You need to implement [RuleInterface](https://github.com/elie29/validator/blob/master/src/Rule/RuleInterface.php) or to extend [AbstractRule](https://github.com/elie29/validator/blob/master/src/Rule/AbstractRule.php)

```php
<?php

use Elie\Validator\Rule\AbstractRule;

class XXXRule extends AbstractRule
{

    public const INVALID_XXX = 'invalidXXX';

    public function __construct(string $key, $value, array $params = [])
    {
        parent::__construct($key, $value, $params);

        // + in order to add unexistant key
        $this->messages += [
            self::INVALID_XXX => '%key%: %value% my message %new_key%'
        ];
    }

    public function validate(): int
    {
        $run = parent::validate();

        if ($run !== $this::CHECK) {
            return $run;
        }

        if ($any_invalid_condition) {
            return $this->setAndReturnError($this::INVALID_XXX, [
                '%new_key%' => 'my_key'
            ]);
        }

        return $this::VALID;
    }
}
```

## Assertion Integration

Instead of using assertion key by key, you can validate the whole context and than use [Assertion](https://github.com/beberlei/assert) or [Assert](https://github.com/webmozart/assert) as follow:

```php
<?php

use Assert\Assertion;
use Elie\Validator\Rule\EmailRule;
use Elie\Validator\Rule\NumericRule;
use Elie\Validator\Rule\StringRule;
use Elie\Validator\Validator;
use Webmozart\Assert\Assert;

$rules =[
    ['age', NumericRule::class, NumericRule::MAX => 60],
    ['name', StringRule::class, StringRule::MIN => 1, StringRule::REQUIRED => true],
    ['email', EmailRule::class, EmailRule::REQUIRED => true],
];

$validator = new Validator($_POST, $rules);

Assert::true($validator->validate(), $validator->getImplodedErrors());

// OR

Assertion::true($validator->validate(), $validator->getImplodedErrors());
```

### Partial Validation

Sometimes we need to validate the context partially, whenever we have a Json item or
keys that depend on each others.

The following is an example when a context - eg. $_POST - should contains a Json user data:

```php
$rules = [
    ['user', JsonRule::class, JsonRule::REQUIRED => true],
];

$validator = new Validator($_POST, $rules);

Assertion::true($validator->validate()); // this assertion validates that the user is in Json format

$validatedPost = $validator->getValidatedContext();

// But we need to validate user data as well (suppose it should contain name and age):

$rules = [
    ['name', StringRule::class, MatchRule::class, MatchRule::PATTERN => '/^[a-z]{1, 20}$/i'],
    ['age', NumericRule::class, NumericRule::MAX => 80],
];
$validator->setRules($rules);

// Decode user as it is a valid JSON
$user = json_decode($validatedPost['user'], true);
$validator->setContext($user); // new context is now user data

Assertion::true($validator->validate()); // this assertion validates user data

/*
Validate accepts a boolean argument - mergedValidatedContext - which is false by default. If set to true
$validator->getValidatedContext() would return:

array:4 [▼
  "email" => "elie29@gmail.com"
  "user" => "{"name": "John Doe", "age": 25}"
  "name" => "John Doe"
  "age" => 25
]
*/
```
## Development Prerequisites

### Text file encoding
- UTF-8

### Code style formatter
- Zend Framework coding standard

### Composer commands
   - `clean`: Cleans all generated files
   - `test`: Launches unit test
   - `test-coverage`: Launches unit test with clover.xml file generation
   - `cs-check`: For code sniffer check
   - `cs-fix`: For code sniffer fix
   - `phpstan`: Launches PHP Static Analysis Tool
   - `check`: Launches `clean`, `cs-check`, `test` and `phpstan`

