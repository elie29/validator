<?php

declare(strict_types = 1);

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

    protected $rules = [];

    /**
     * Params could have the following structure:
     * [
     *   'required' => {bool:optional:false by default},
     *   'messages' => {array:optional:key/value message patterns},
     *   'rules' => {array:optional:list of rules with their params},
     * ]
     * <code>
     *    should be string and email
     *    $params = [<br/>
     *      'required' => true,<br/>
     *      'rules' => [<br/>
     *         [StringRule::class, 'min' => 1, 'max' => 255],<br/>
     *         [EmailRule::class],<br/>
     *      ]
     *    ]
     * </code>
     *
     * Value is considered valid if 'rules' is empty
     */
    public function __construct(string $key, $value, array $params = [])
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
        foreach ($this->rules as $rule) {
            $class = $this->resolve($rule);
            if ($class->validate() === RuleInterface::ERROR) {
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
