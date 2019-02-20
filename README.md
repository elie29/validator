# Validator Project

[![Build Status](https://travis-ci.org/elie29/validator.svg?branch=master)](https://travis-ci.org/elie29/validator)
[![Coverage Status](https://coveralls.io/repos/github/elie29/validator/badge.svg)](https://coveralls.io/github/elie29/validator)

## Introduction
A library for validating a context (POST, GET etc...) by running given rules.

## Text file encoding
- UTF-8

## Code style formatter
- Zend Framework coding standard

## Composer commands
   - `clean`: Clean all generated files
   - `test`: Launch unit test
   - `test-coverage`: Launch unit test with clover.xml file generation
   - `cs-check`: code sniffer check
   - `cs-fix`: code sniffer fix
   - `phpstan`: Launch PHP Static Analysis Tool
   - `check`: Launch `clean`, `cs-check`, `test` and `phpstan`

## Installation ##

Run the command below to install via Composer:

```shell
composer require elie29/validator
```

## Getting Started ##
`Validator` requires one or several rules ([constraints](#available-rules)) in order to validate a given context.

A basic example with $_GET
```php
<?php

use Elie\Validator\Rule\EmailRule;
use Elie\Validator\Rule\NumericRule;
use Elie\Validator\Rule\StringRule;
use Elie\Validator\Validator;

$validator = new Validator($_POST, true); // stop processing on error.

/**
 * A key could have multiple rules
 *  - age could be empty (unexistant, null or '') otherwise NumericRule is applied
 *  - name could not be empty (required and minimum 1 character length)
 *  - email is required and should be a valid email
 */
$validator->setRules([
    ['age', NumericRule::class, 'max' => 60],
    ['name', StringRule::class, 'min' => 1, 'required' => true],
    ['email', EmailRule::class, 'required' => true],
]);

$validator->validate(); // bool depends on $_POST content
```
### Available rules ###

1. [BicRule](https://github.com/elie29/validator/blob/master/src/Rule/BicRule.php)
1. [BooleanRule](https://github.com/elie29/validator/blob/master/src/Rule/BooleanRule.php)
1. [BooleanRule](https://github.com/elie29/validator/blob/master/src/Rule/BooleanRule.php)
1. [DateRule](https://github.com/elie29/validator/blob/master/src/Rule/DateRule.php)
1. [EmailRule](https://github.com/elie29/validator/blob/master/src/Rule/EmailRule.php)
1. [IpRule](https://github.com/elie29/validator/blob/master/src/Rule/IpRule.php)
1. [JsonRule](https://github.com/elie29/validator/blob/master/src/Rule/JsonRule.php)
1. [MatchRule](https://github.com/elie29/validator/blob/master/src/Rule/MatchRule.php)
1. [NumericRule](https://github.com/elie29/validator/blob/master/src/Rule/NumericRule.php)
1. [RangeRule](https://github.com/elie29/validator/blob/master/src/Rule/RangeRule.php)
1. [StringRule](https://github.com/elie29/validator/blob/master/src/Rule/StringRule.php)
1. [TimeRule](https://github.com/elie29/validator/blob/master/src/Rule/TimeRule.php)
1. [Your own rule](#how-to-add-a-new-rule)

### How to add a new rule ###

You need to implement [RuleInterface](https://github.com/elie29/validator/blob/master/src/Rule/RuleInterface.php) or to extend [AbstractRule](https://github.com/elie29/validator/blob/master/src/Rule/AbstractRule.php)

