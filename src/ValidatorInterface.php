<?php

declare(strict_types=1);

namespace Elie\Validator;

/**
 * Validator interface that validates context by running all given rules.
 */
interface ValidatorInterface
{

    /**
     * Assign a context to be validated.
     *
     * @param array $context Associative array of keys to be validated.
     *     Each key should correspond to the rule's key to be validated.
     *
     * @param array $rules List of rules {@see setRules} to validate the context.
     *
     * @param bool $stopOnError Defaults to false, meaning all rules will be validated regardless of errors found.
     */
    public function __construct(array $context, array $rules = [], bool $stopOnError = false);

    /**
     * Set a new context to be validated.
     *
     * @param array $context Array containing a list of values to be validated such as $_POST, $_GET or any data.
     *
     * @return ValidatorInterface For method chaining.
     */
    public function setContext(array $context): ValidatorInterface;

    /**
     * Retrieves validated context.
     *
     * @return array
     */
    public function getValidatedContext(): array;

    /**
     * Set validation rules for the context.
     *
     * Each rule should have the following structure:
     * <ul>
     *   <li>key is used to find its associated value in the context.
     *   <li>rule classname used for validation
     *   <li>list of rule class parameters if needed
     * </ul>
     * A key could have more than one validator class.
     *
     * <code>
     *    $rules = [
     *      ['age', NumericRule::class, NumericRule::MIN => 1, NumericRule::MAX => 99],
     *      ['role', RangeRule::class, RangeRule::RANGE => ['M', 'U', 'E']],
     *    ]
     * </code>
     *
     * @param array $rules Rules to be set.
     *
     * @return ValidatorInterface For method chaining.
     */
    public function setRules(array $rules): ValidatorInterface;

    /**
     * Return all set rules.
     *
     * @return array
     */
    public function getRules(): array;

    /**
     * Control whether to append only existing context keys to validated context.
     *
     * @param bool $value True to append only keys found in the context to the validated context.
     *                    False (default) to add all keys set in the rules to the validated context.
     *
     * @return ValidatorInterface For method chaining.
     */
    public function appendExistingItemsOnly(bool $value): ValidatorInterface;

    /**
     * Return all errors found.
     *
     * @return array
     */
    public function getErrors(): array;

    /**
     * Return all errors found.
     *
     * @param string $separator Default separator is .
     *
     * @return string
     */
    public function getImplodedErrors(string $separator = ''): string;

    /**
     * Retrieves the value of a requested key from the {@link context}.
     *
     * @param int|string $key Key to be got.
     *
     * @return mixed The retrieved value.
     */
    public function get(int|string $key): mixed;

    /**
     * Whether to stop on error or not.
     *
     * @return bool
     */
    public function shouldStopOnError(): bool;

    /**
     * Set whether to stop validation on first error.
     *
     * @param bool $stopOnError True to stop validation when the first error occurs.
     *                          False (default) to validate all rules regardless of errors.
     *
     * @return ValidatorInterface For method chaining.
     */
    public function setStopOnError(bool $stopOnError): ValidatorInterface;

    /**
     * Each rule is executed. If an error is found and stop on error
     * is set to true, we stop the process and we return false.
     *
     * Keys context will be ignored if they don't have any rule.
     *
     * @param bool $mergeValidatedContext Merge validated values with existing one.
     *  Useful with partial validation
     *
     * @return bool
     */
    public function validate(bool $mergeValidatedContext = false): bool;
}
