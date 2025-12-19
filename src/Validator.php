<?php

declare(strict_types=1);

namespace Elie\Validator;

use Elie\Validator\Rule\RuleInterface;

class Validator implements ValidatorInterface
{

    /**
     * Context to be validated.
     */
    protected array $context = [];

    /**
     * Validated context.
     */
    protected array $validatedContext = [];

    /**
     * Rules that validate context.
     */
    protected array $rules = [];

    /**
     * Defaults to false, meaning that validation
     * won't stop when an error is encountered.
     */
    protected bool $stopOnError = false;

    /**
     * Defaults to false, meaning that validation
     * will append the key to the validated context if
     * it is not found.
     */
    protected bool $appendExistingItemOnly = false;

    /**
     * Contains the error(s) found during rule validation.
     */
    protected array $errors = [];

    public function __construct(array $context, array $rules = [], bool $stopOnError = false)
    {
        $this->setContext($context);
        $this->setRules($rules);
        $this->setStopOnError($stopOnError);
    }

    /**
     * Set a new context to be validated.
     *
     * @param array $context Array containing values to be validated.
     *
     * @return static For method chaining.
     */
    public function setContext(array $context): static
    {
        $this->context = $context;
        return $this;
    }

    public function getValidatedContext(): array
    {
        return $this->validatedContext;
    }

    public function getRules(): array
    {
        return $this->rules;
    }

    /**
     * Set validation rules for the context.
     *
     * @param array $rules Rules to be set.
     *
     * @return static For method chaining.
     */
    public function setRules(array $rules): static
    {
        $this->rules = $rules;
        return $this;
    }

    /**
     * Control whether to append only existing context keys to validated context.
     *
     * @param bool $value True to append only keys found in the context.
     *                    False (default) to add all keys set in the rules.
     *
     * @return static For method chaining.
     */
    public function appendExistingItemsOnly(bool $value): static
    {
        $this->appendExistingItemOnly = $value;
        return $this;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getImplodedErrors(string $separator = '<br/>'): string
    {
        return implode($separator, $this->errors);
    }

    public function get(int|string $key): mixed
    {
        return $this->context[$key] ?? null;
    }

    public function shouldStopOnError(): bool
    {
        return $this->stopOnError;
    }

    /**
     * Set whether to stop validation on first error.
     *
     * @param bool $stopOnError True to stop on first error, false to validate all rules.
     *
     * @return static For method chaining.
     */
    public function setStopOnError(bool $stopOnError): static
    {
        $this->stopOnError = $stopOnError;
        return $this;
    }

    public function validate(bool $mergeValidatedContext = false): bool
    {
        $this->validatedContext = $mergeValidatedContext ? $this->validatedContext : [];

        // All rules supposed OK
        $res = true;
        $this->errors = [];

        foreach ($this->rules as $ruleValue) {
            $rule = $this->resolve($ruleValue);

            if ($rule->validate() !== RuleInterface::ERROR) {
                $this->addKeyToValidatedContext($rule);
                continue;
            }

            $this->errors[] = $rule->getError();
            $res = false;

            if ($this->stopOnError) {
                return false;
            }
        }

        return $res;
    }

    protected function resolve(array $rule): RuleInterface
    {
        // The first element must be the key context
        $key = $rule[0];
        // The second element must be the class validator name
        $class = $rule[1];

        return new $class($key, $this->get($key), $rule);
    }

    protected function addKeyToValidatedContext(RuleInterface $rule): void
    {
        $key = $rule->getKey();

        if ($this->appendExistingItemOnly && !array_key_exists($key, $this->context)) {
            return;
        }

        $this->validatedContext[$key] = $rule->getValue();
    }
}
