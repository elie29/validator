<?php

declare(strict_types = 1);

namespace Elie\Validator;

use Elie\Validator\Rule\RuleInterface;

class Validator implements ValidatorInterface
{

    /**
     * Context to be validated.
     * @var array
     */
    protected $context = [];

    /**
     * Validated context.
     * @var array
     */
    protected $validatedContext = [];

    /**
     * Rules that validate context.
     * @var array
     */
    protected $rules = [];

    /**
     * Defaults to false, meaning that validation
     * won't stop when an error is encountered.
     * @var bool
     */
    protected $stopOnError = false;

    /**
     * Defaults to false, meaning that validation
     * will append the key to the validated context if
     * it is not found.
     * @var bool
     */
    protected $appendExistingItemOnly = false;

    /**
     * Contains the error(s) found during rules validation.
     * @var array
     */
    protected $errors = [];

    public function __construct(array $context, array $rules = [], bool $stopOnError = false)
    {
        $this->setContext($context);
        $this->setRules($rules);
        $this->setStopOnError($stopOnError);
    }

    public function setContext(array $context): self
    {
        $this->context = $context;
        return $this;
    }

    public function getValidatedContext(): array
    {
        return $this->validatedContext;
    }

    public function setRules(array $rules): self
    {
        $this->rules = $rules;
        // Keep chaining
        return $this;
    }

    public function getRules(): array
    {
        return $this->rules;
    }

    public function appendExistingItemsOnly(bool $choice): self
    {
        $this->appendExistingItemOnly = $choice;
        // Keep chaining
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

    public function get($key)
    {
        return $this->context[$key] ?? null;
    }

    public function shouldStopOnError(): bool
    {
        return $this->stopOnError;
    }

    public function setStopOnError(bool $stopOnError): self
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
                return $res;
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

        if ($this->appendExistingItemOnly && ! array_key_exists($key, $this->context)) {
            return;
        }

        $this->validatedContext[$key] = $rule->getValue();
    }
}
