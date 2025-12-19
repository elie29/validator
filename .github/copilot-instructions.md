# AI Agent Instructions for Validator Library

## Project Overview

This is a PHP 8.2+ validation library (`elie29/validator`) that validates context arrays (like $_POST, $_GET) using composable rule-based validation. The architecture follows a rule-chain pattern where each field can have multiple validators applied sequentially.

**Key namespace**: `Elie\Validator` - All classes live under this PSR-4 autoloaded namespace.

## Core Architecture

### Main Components

1. **[Validator](../src/Validator.php)** - The orchestrator that applies rules to context arrays and builds validated output
2. **[RuleInterface](../src/Rule/RuleInterface.php)** - Defines validation contract with three return states: `ERROR` (0), `VALID` (1), `CHECK` (2)
3. **[AbstractRule](../src/Rule/AbstractRule.php)** - Base class all rules extend, handles common params (`required`, `trim`, `messages`)
4. **20+ Concrete Rules** in [src/Rule/](../src/Rule/) - Each validates specific data types/patterns

### Rule Validation Flow

Rules return status codes from `validate()`:
- `CHECK` (2) = "not empty, keep validating" - signals to child classes to continue validation
- `VALID` (1) = validation passed
- `ERROR` (0) = validation failed, error message set in `$this->error`

Example from [AbstractRule::validate()](../src/Rule/AbstractRule.php#L92-L100):
```php
if (!$this->isEmpty()) {
    return $this::CHECK; // Continue validation in child class
}
return $this->required ? $this::ERROR : $this::VALID;
```

### Rule Composition Patterns

**MultipleAndRule** - All rules must pass (logical AND):
```php
['email', MultipleAndRule::class, MultipleAndRule::RULES => [
    [StringRule::class, StringRule::MAX => 255],
    [EmailRule::class],
]]
```

**MultipleOrRule** - At least one rule must pass (logical OR)

**CollectionRule** - Validates nested arrays, supports JSON decoding via `JSON => true`

## Critical Conventions

### Rule Parameter Structure

Every rule constructor follows this signature:
```php
public function __construct(int|string $key, mixed $value, array $params = [])
```

The `$params` array uses **class constants as keys**, not strings:
```php
// CORRECT
['name', StringRule::class, StringRule::MIN => 1, StringRule::REQUIRED => true]

// WRONG - don't use 'min', 'required' as strings
['name', StringRule::class, 'min' => 1, 'required' => true]
```

### Value Trimming

All rules **trim string values by default** (`trim => true`). This is critical - values in validated context will be trimmed unless explicitly disabled.

### Validated Context Behavior

After `$validator->validate()`, retrieve processed values via `getValidatedContext()`, NOT from original context:
- Values may be trimmed, cast (e.g., `BooleanRule::CAST`, `NumericRule::CAST`), or transformed
- Use `appendExistingItemsOnly(true)` to exclude missing keys from validated context

## Development Workflows

### Running Tests

```bash
composer test                  # Fast test without coverage
composer test-coverage         # With Xdebug coverage (generates clover.xml)
composer cover                 # Run coverage + serve HTML report at localhost:5001
composer clean                 # Remove build artifacts
```

Tests use PHPUnit 11.5 with custom bootstrap at [tests/bootstrap.php](../tests/bootstrap.php).

### CI/CD

GitHub Actions workflow tests against PHP 8.2, 8.3, 8.4, 8.5 matrix. Coverage reports push to Coveralls for PHP 8.2 only.

### Adding New Rules

1. Extend [AbstractRule](../src/Rule/AbstractRule.php) or implement [RuleInterface](../src/Rule/RuleInterface.php)
2. Define error code constants (e.g., `INVALID_MY_VALUE`)
3. Merge custom error messages in constructor using `+=` operator:
   ```php
   $this->messages += [self::INVALID_MY_VALUE => '%key%: custom message'];
   ```
4. Call `parent::validate()` first, return if not `CHECK`, then apply your logic
5. Return `setAndReturnError($code, $replacements)` on failure or `VALID` on success
6. See [README.md example](../README.md#how-to-add-a-new-rule) for complete pattern

### Test Structure

- Each rule has a corresponding test in [tests/Rule/](../tests/Rule/) (e.g., `StringRuleTest.php`)
- Use `#[DataProviderExternal]` attribute to pull test data from [Stub/DataProvider.php](../tests/Stub/DataProvider.php)
- Test both valid and invalid scenarios, verify error codes and messages

## Common Pitfalls

1. **Don't forget to call `parent::validate()`** in custom rules - it handles `required` and empty value logic
2. **Error messages use placeholder patterns** - `%key%`, `%value%`, `%code%` are auto-replaced, custom params need manual replacement
3. **Values are trimmed by default** - test with whitespace awareness
4. **Rules are chained per key** - multiple rules for same key run sequentially unless using composition (MultipleAndRule)

## Helper Classes

[Helper/Text.php](../src/Helper/Text.php) - Utility for string manipulation (used by StringCleanerRule)

## External Dependencies

- `ext-json` - Required for JsonRule/CollectionRule JSON parsing
- `ext-ctype` - Used by various rules for character type checking
- PHPUnit 11.5, Symfony VarDumper (dev only)
