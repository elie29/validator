<?php

declare(strict_types=1);

namespace Elie\Validator\Rule;

/**
 * This class acts as a decorator to validate a key through all provided rules.
 */
class MultipleAndRule extends AbstractRule
{

    /**
     * Specific options for MultipleArrayRule
     */
    public const RULES = 'rules';

    protected array $rules = [];

    /**
     * Params could have the following structure:
     * <code>
     * [
     *   'required' => {bool:optional:false by default},
     *   'messages' => {array:optional:key/value message patterns},
     *   'rules' => {array:optional:list of rules with their params},
     * ]
     * </code>
     * Should be string and email:
     * <code>
     *    $params = [
     *      'required' => true,
     *      'rules' => [
     *         [StringRule::class, 'min' => 1, 'max' => 255],
     *         [EmailRule::class],
     *      ]
     *    ]
     * </code>
     *
     * Value is considered valid if 'rules' are empty
     */
    public function __construct(int|string $key, mixed $value, array $params = [])
    {
        parent::__construct($key, $value, $params);

        if (isset($params[$this::RULES])) {
            $this->rules = $params[$this::RULES];
        }
    }

    public function validate(): int
    {
        $run = parent::validate();

        if ($run !== $this::CHECK) {
            return $run;
        }

        return $this->rules === [] ? $this::VALID : $this->isValid();
    }

    protected function isValid(): int
    {
        $this->error = '';

        foreach ($this->rules as $rule) {
            $class = $this->resolve($rule);
            if ($class->validate() === RuleInterface::ERROR) {
                $this->error = $class->getError();
                return RuleInterface::ERROR;
            }
        }

        return RuleInterface::VALID;
    }

    protected function resolve(array $rule): RuleInterface
    {
        $class = $rule[0];

        return new $class($this->key, $this->value, $rule);
    }
}
