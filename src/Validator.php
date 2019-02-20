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
     * Validated coontext.
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
     * Contains the error(s) found during rules validation.
     * @var array
     */
    protected $errors = [];

    public function __construct(array $context, bool $stopOnError = false)
    {
        $this->setContext($context);
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

    public function setRules(array $rules):self
    {
        $this->rules = $rules;
        // Keep chaining
        return $this;
    }

    public function getRules(): array
    {
        return $this->rules;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function get(string $key)
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

    public function validate(): bool
    {
        $this->validatedContext = [];

        // All rules supposed OK
        $res = true;
        $this->errors = [];

        foreach ($this->rules as $ruleValue) {
            $rule = $this->resolve($ruleValue);

            if ($rule->validate() !== RuleInterface::ERROR) {
                $this->validatedContext[$rule->getKey()] = $rule->getValue();
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
        // The second element must be the class valdiator name
        $class = $rule[1];

        return new $class($key, $this->get($key), $rule);
    }
}
