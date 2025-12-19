# Validator Project

[![build](https://github.com/elie29/validator/actions/workflows/php-build.yml/badge.svg)](https://github.com/elie29/validator/actions/workflows/php-build.yml)
[![Coverage Status](https://coveralls.io/repos/github/elie29/validator/badge.svg)](https://coveralls.io/github/elie29/validator)
[![PHP Version](https://img.shields.io/packagist/php-v/elie29/validator.svg)](https://packagist.org/packages/elie29/validator)

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

if ($validator->validate()) {
    // Validation passed - use validated context
    $validatedData = $validator->getValidatedContext();
    // Process $validatedData safely
} else {
    // Validation failed - handle errors
    $errors = $validator->getErrors(); // array of error messages
    // or get formatted string:
    echo $validator->getImplodedErrors(); // errors separated by <br/>
}
```

## Common Usage Patterns

### Basic Form Validation

```php
use Elie\Validator\Validator;
use Elie\Validator\Rule\{EmailRule, StringRule, NumericRule};

// Validate a contact form
$rules = [
    ['name', StringRule::class, StringRule::MIN => 2, StringRule::MAX => 100, StringRule::REQUIRED => true],
    ['email', EmailRule::class, EmailRule::REQUIRED => true],
    ['age', NumericRule::class, NumericRule::MIN => 18, NumericRule::MAX => 120],
    ['message', StringRule::class, StringRule::MIN => 10, StringRule::MAX => 1000],
];

$validator = new Validator($_POST, $rules);

if ($validator->validate()) {
    $data = $validator->getValidatedContext();
    // All values are trimmed by default and validated
    sendEmail($data['email'], $data['name'], $data['message']);
} else {
    // Display all errors to user
    foreach ($validator->getErrors() as $error) {
        echo "<p class='error'>$error</p>";
    }
}
```

### API Request Validation

```php
use Elie\Validator\Validator;
use Elie\Validator\Rule\{ChoicesRule, BooleanRule, NumericRule};

// Validate API query parameters
$rules = [
    ['page', NumericRule::class, NumericRule::MIN => 1, NumericRule::CAST => true],
    ['limit', NumericRule::class, NumericRule::MIN => 1, NumericRule::MAX => 100, NumericRule::CAST => true],
    ['sort', ChoicesRule::class, ChoicesRule::LIST => ['asc', 'desc']],
    ['active', BooleanRule::class, BooleanRule::CAST => true],
];

$validator = new Validator($_GET, $rules);
$validator->appendExistingItemsOnly(true); // Don't include missing optional parameters

if ($validator->validate()) {
    $params = $validator->getValidatedContext();
    // $params only contains provided parameters, properly cast
    $results = fetchRecords($params);
    return json_encode($results);
} else {
    http_response_code(400);
    return json_encode(['errors' => $validator->getErrors()]);
}
```

### Working with Optional Fields

```php
// Fields that aren't required can be omitted or empty
$rules = [
    ['username', StringRule::class, StringRule::MIN => 3, StringRule::REQUIRED => true],
    ['bio', StringRule::class, StringRule::MAX => 500], // Optional, only validated if provided
    ['website', EmailRule::class], // Optional URL field
];

$validator = new Validator($data, $rules);
$validator->appendExistingItemsOnly(true); // Exclude missing optional fields from output

if ($validator->validate()) {
    $validatedData = $validator->getValidatedContext();
    // $validatedData only contains 'username' and any optional fields that were provided
}
```

### Conditional Validation

```php
use Elie\Validator\Rule\{MultipleOrRule, EmailRule, MatchRule};

// Accept either email OR phone number
$rules = [
    ['contact', MultipleOrRule::class, MultipleOrRule::REQUIRED => true, MultipleOrRule::RULES => [
        [EmailRule::class],
        [MatchRule::class, MatchRule::PATTERN => '/^\+?[1-9]\d{1,14}$/'], // E.164 phone format
    ]],
];

$validator = new Validator(['contact' => 'user@example.com'], $rules);
$validator->validate(); // true - email is valid

$validator->setContext(['contact' => '+1234567890']);
$validator->validate(); // true - phone is valid
```

### Custom Error Messages

```php
use Elie\Validator\Rule\{StringRule, RuleInterface};

$rules = [
    ['username', StringRule::class, 
        StringRule::MIN => 3,
        StringRule::MAX => 20,
        StringRule::REQUIRED => true,
        RuleInterface::MESSAGES => [
            RuleInterface::EMPTY_KEY => 'Username is required',
            StringRule::INVALID_MIN_VALUE => 'Username must be at least 3 characters',
            StringRule::INVALID_MAX_VALUE => 'Username cannot exceed 20 characters',
        ]
    ],
];

$validator = new Validator(['username' => 'ab'], $rules);
if (!$validator->validate()) {
    echo $validator->getImplodedErrors(); // "Username must be at least 3 characters"
}
```

### Handling Whitespace

```php
// By default, all string values are trimmed
$rules = [
    ['title', StringRule::class, StringRule::MIN => 1], // Leading/trailing spaces removed
];

// To preserve whitespace:
$rules = [
    ['code', StringRule::class, StringRule::MIN => 1, 'trim' => false],
];

$validator = new Validator(['title' => '  Hello  '], $rules);
$validator->validate();
$validated = $validator->getValidatedContext();
// With trim=true (default): $validated['title'] = 'Hello'
// With trim=false: $validated['title'] = '  Hello  '
```

### Stop on First Error

```php
// Third parameter controls error handling behavior
$validator = new Validator($data, $rules, true); // Stops at first error
$validator->validate();

// OR set it later
$validator = new Validator($data, $rules);
$validator->setStopOnError(true);

if (!$validator->validate()) {
    // Only the first error will be in getErrors()
    $firstError = $validator->getErrors()[0];
}
```

### Available rules

1. [All Rules](https://github.com/elie29/validator/blob/master/src/Rule/AbstractRule.php) accept `required`, `trim` and
   `messages` options.
   `required` is false by default while `trim` is true.
2. [ArrayRule](https://github.com/elie29/validator/blob/master/src/Rule/ArrayRule.php) accepts `min` and `max` options.
   Empty value is cast to an empty array [].
3. [BicRule](https://github.com/elie29/validator/blob/master/src/Rule/BicRule.php) validates Bank Identifier Code (
   SWIFT-BIC).
4. [BooleanRule](https://github.com/elie29/validator/blob/master/src/Rule/BooleanRule.php) accepts `cast` option.
5. [CallableRule](https://github.com/elie29/validator/blob/master/src/Rule/CallableRule.php) accepts `callable`
   function.
6. [ChoicesRule](https://github.com/elie29/validator/blob/master/src/Rule/ChoicesRule.php) accepts `list` option.
7. [CollectionRule](https://github.com/elie29/validator/blob/master/src/Rule/CollectionRule.php) accepts `rules` and
   `json` options.
8. [CompareRule](https://github.com/elie29/validator/blob/master/src/Rule/CompareRule.php) accepts `sign` and `expected`
   options. `sign` is [CompareRule::EQ](https://github.com/elie29/validator/blob/master/src/Rule/CompareConstants.php)
   by default, `expected` is null by default.
9. [DateRule](https://github.com/elie29/validator/blob/master/src/Rule/DateRule.php) accepts `format` and `separator`
   options.
10. [EmailRule](https://github.com/elie29/validator/blob/master/src/Rule/EmailRule.php) validates email addresses.
11. [IpRule](https://github.com/elie29/validator/blob/master/src/Rule/IpRule.php) accepts `flag` option.
12. [JsonRule](https://github.com/elie29/validator/blob/master/src/Rule/JsonRule.php) accepts `decode` option.
13. [MatchRule](https://github.com/elie29/validator/blob/master/src/Rule/MatchRule.php) requires `pattern` option.
14. [MultipleAndRule](https://github.com/elie29/validator/blob/master/src/Rule/MultipleAndRule.php) requires `rules`
    option (all rules must pass).
15. [MultipleOrRule](https://github.com/elie29/validator/blob/master/src/Rule/MultipleOrRule.php) requires `rules`
    option (at least one rule must pass).
16. [NumericRule](https://github.com/elie29/validator/blob/master/src/Rule/NumericRule.php) accepts `min`, `max` and
    `cast` options.
17. [RangeRule](https://github.com/elie29/validator/blob/master/src/Rule/RangeRule.php) accepts `range` option.
18. [StringCleanerRule](https://github.com/elie29/validator/blob/master/src/Rule/StringCleanerRule.php) removes
    invisible characters from strings.
19. [StringRule](https://github.com/elie29/validator/blob/master/src/Rule/StringRule.php) accepts `min` and `max`
    options.
20. [TimeRule](https://github.com/elie29/validator/blob/master/src/Rule/TimeRule.php) validates time format.
21. [Your own rule](#how-to-add-a-new-rule)

### How to add a new rule

You need to implement [RuleInterface](https://github.com/elie29/validator/blob/master/src/Rule/RuleInterface.php) or to
extend [AbstractRule](https://github.com/elie29/validator/blob/master/src/Rule/AbstractRule.php)

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

## Understanding Validated Context

The validated context is the processed output after validation runs. It's **not the same** as your input data:

### Key Differences from Input

```php
use Elie\Validator\Rule\{StringRule, NumericRule, BooleanRule};

$input = [
    'name' => '  John Doe  ',    // Has whitespace
    'age' => '25',               // String representation
    'active' => 'true',          // String boolean
];

$rules = [
    ['name', StringRule::class],
    ['age', NumericRule::class, NumericRule::CAST => true],
    ['active', BooleanRule::class, BooleanRule::CAST => true],
];

$validator = new Validator($input, $rules);
$validator->validate();

$validated = $validator->getValidatedContext();

// Results:
// $validated['name'] = 'John Doe'     // Trimmed
// $validated['age'] = 25              // Cast to int
// $validated['active'] = true         // Cast to bool

// Original input is unchanged:
// $input['name'] = '  John Doe  '
// $input['age'] = '25'
```

### Controlling Output Keys

By default, all rules' keys appear in validated context, even if not in input:

```php
$input = ['username' => 'john'];

$rules = [
    ['username', StringRule::class],
    ['email', EmailRule::class], // Not in input
];

$validator = new Validator($input, $rules);
$validator->validate();

// Default behavior:
$validated = $validator->getValidatedContext();
// $validated = ['username' => 'john', 'email' => null]

// To exclude missing keys:
$validator->appendExistingItemsOnly(true);
$validator->validate();
$validated = $validator->getValidatedContext();
// $validated = ['username' => 'john']
```

### When to Use appendExistingItemsOnly(true)

- **API endpoints** - Only return fields that were provided
- **Partial updates** - PATCH operations where only some fields update
- **Optional configurations** - Settings where absence has meaning

```php
// Example: Partial user profile update
$rules = [
    ['name', StringRule::class, StringRule::MAX => 100],
    ['bio', StringRule::class, StringRule::MAX => 500],
    ['website', StringRule::class, StringRule::MAX => 255],
];

$validator = new Validator($_POST, $rules);
$validator->appendExistingItemsOnly(true);

if ($validator->validate()) {
    $updates = $validator->getValidatedContext();
    // Only update fields that were actually submitted
    updateUserProfile($userId, $updates);
}
```

## Assertion Integration

Instead of using assertion key by key, you can validate the whole context and then
use [Assertion](https://github.com/beberlei/assert) or [Assert](https://github.com/webmozart/assert) as follows:

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

A new [CollectionRule](https://github.com/elie29/validator/blob/master/src/Rule/CollectionRule.php) has been added to
validate collection data (array or JSON) as follows:

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

## Best Practices and Tips

### Rule Parameter Keys

**Always use class constants for rule parameters**, not strings:

```php
// ✅ CORRECT
['age', NumericRule::class, NumericRule::MIN => 18, NumericRule::MAX => 120]

// ❌ WRONG - 'min' and 'max' as strings won't work
['age', NumericRule::class, 'min' => 18, 'max' => 120]
```

### Validation Flow Pattern

```php
// Recommended pattern for all validations:
$validator = new Validator($inputData, $rules);

if (!$validator->validate()) {
    // Handle errors first
    logErrors($validator->getErrors());
    return ['success' => false, 'errors' => $validator->getErrors()];
}

// Only proceed with validated data
$safeData = $validator->getValidatedContext();
processData($safeData);
```

### Reusing Validator Instances

Validators can be reused for multiple validations:

```php
$validator = new Validator([], $userRules);

foreach ($batchData as $userData) {
    $validator->setContext($userData);
    
    if ($validator->validate()) {
        processUser($validator->getValidatedContext());
    } else {
        logErrors($userData['id'], $validator->getErrors());
    }
}
```

### Composition vs Multiple Rules

```php
// When rules must ALL pass, use MultipleAndRule:
['email', MultipleAndRule::class, MultipleAndRule::RULES => [
    [StringRule::class, StringRule::MAX => 255],
    [EmailRule::class],
]]

// When ANY rule can pass, use MultipleOrRule:
['identifier', MultipleOrRule::class, MultipleOrRule::RULES => [
    [EmailRule::class],
    [MatchRule::class, MatchRule::PATTERN => '/^\d{10}$/'], // 10-digit ID
]]

// Avoid chaining the same key twice - use composition instead:
// ❌ Less efficient:
['email', StringRule::class, StringRule::MAX => 255],
['email', EmailRule::class],

// ✅ Better - uses composition:
['email', MultipleAndRule::class, MultipleAndRule::RULES => [
    [StringRule::class, StringRule::MAX => 255],
    [EmailRule::class],
]]
```

### Testing Your Validations

```php
use PHPUnit\Framework\TestCase;
use Elie\Validator\Rule\RuleInterface;

class UserValidatorTest extends TestCase
{
    public function testValidUserData(): void
    {
        $validator = new Validator(
            ['username' => 'john', 'age' => 25],
            $this->getUserRules()
        );
        
        $this->assertTrue($validator->validate());
        $this->assertEmpty($validator->getErrors());
    }
    
    public function testInvalidAge(): void
    {
        $validator = new Validator(
            ['username' => 'john', 'age' => 150],
            $this->getUserRules()
        );
        
        $this->assertFalse($validator->validate());
        $this->assertNotEmpty($validator->getErrors());
        $this->assertStringContainsString('age', $validator->getImplodedErrors());
    }
}
```

### Performance Considerations

- Use `stopOnError => true` for large datasets when you only need to know if validation passed
- Use `appendExistingItemsOnly(true)` to reduce memory footprint with sparse data
- Reuse validator instances instead of creating new ones in loops
- For nested/complex validations, use CollectionRule instead of manual iteration

## Development Prerequisites

### Text file encoding

- UTF-8

### Composer commands

- `composer test`: Runs unit tests without coverage
- `composer test-coverage`: Runs unit tests with code coverage (requires Xdebug)
- `composer cover`: Runs tests with coverage and starts a local server to view coverage report
  at <http://localhost:5001>
- `composer clean`: Cleans all generated files (build directory and clover.xml)
