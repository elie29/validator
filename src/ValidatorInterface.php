<?php

declare(strict_types = 1);

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
     *     Each key should correspond to rule's key to be validated.
     *
     * @param array $rules List of rules {@see setRules} in order to
     *     validate the context.
     *
     * @param bool  $stopOnError Defaults to false, meaning
     *     all rules will be validated regardless errors found.
     */
    public function __construct(array $context, array $rules = [], bool $stopOnError = false);

    /**
     * Add a new context to be validated.
     *
     * @param array $context Associative array containing a list of
     *     values to be validated such as a post or get or any information.
     *
     * @return ValidatorInterface
     */
    public function setContext(array $context);

    /**
     * Retrieves validated context.
     *
     * @return array
     */
    public function getValidatedContext(): array;

    /**
     * List of rules that should have each the following structure:
     * <ul>
     *   <li>key is used to find its associated value in the context.
     *   <li>rule classname used for validation
     *   <li>list of rule class parameters if needed
     * </ul>
     * A key could have more than one validator class.
     *
     * <code>
     *    $rules = [<br/>
     *      ['age',  NumericRule::class, 'min' => 1, 'max' => 99],<br/>
     *      ['role', RangeRule::class, 'range' => ['M', 'U', 'E']],<br/>
     *    ]
     * </code>
     *
     * @param array $rules Rules to be set.
     *
     * @return ValidatorInterface
     */
    public function setRules(array $rules);

    /**
     * Return all set rules.
     *
     * @return array
     */
    public function getRules(): array;

    /**
     * @param bool $value True, append only found keys in the context to the validated context.
     * Defaults to false, so all keys set in the rules will be added to the validated context.
     */
    public function appendExistingItemsOnly(bool $value);

    /**
     * Return all errors found.
     *
     * @return array
     */
    public function getErrors(): array;

    /**
     * Return all errors found.
     *
     * @return string
     */
    public function getImplodedErrors(string $separator = '<br/>'): string;

    /**
     * Retrieves the value of a requested key from the {@link context}.
     *
     * @param string $key Key to be get.
     *
     * @return mixed|null The retrieved value.
     */
    public function get(string $key);

    /**
     * Whether to stop on error or not.
     *
     * @return bool
     */
    public function shouldStopOnError(): bool;

    /**
     * Change stopOnError value.
     *
     * @return ValidatorInterface
     */
    public function setStopOnError(bool $stopOnError);

    /**
     * Each rule is executed. If error is found and stop on error
     * is set to true, we stop the process and we return false.
     *
     * keys context will be ignored if they don't have any rule.
     *
     * @param bool $mergeValidatedContext Merge validated values with existing one.
     *  Useful with partial validation
     *
     * @return bool
     */
    public function validate(bool $mergeValidatedContext = false): bool;
}
