<?php

declare(strict_types=1);

namespace Elie\Validator\Rule;

/**
 * This class validates an array of data (it could be a JSON array and would be decoded).
 */
class CollectionRule extends AbstractRule
{

    /**
     * Specific message error code
     */
    public const INVALID_VALUE = 'INVALID_VALUE';

    /**#@+
     * Specific options for CollectionRule
     */
    public const RULES = 'rules';
    public const JSON = 'json';
    /**#@-*/

    protected array $rules = [];

    protected bool $decode = false; // for json value

    /**
     * Params could have the following structure:
     * <code>
     * [
     *   'required' => {bool:optional:false by default},
     *   'messages' => {array:optional:key/value message patterns},
     *   'rules' => {array:optional:list of rules with their params},
     *   'json' => {boolean:optional:false by default},
     * ]
     * </code>
     * <code>
     *    $params = [
     *      'required' => true,
     *      'rules' => [
     *         ['code', StringRule::class, 'min' => 1, 'max' => 255],
     *         ['email', EmailRule::class],
     *      ]
     *    ]
     * </code>
     *
     * Value is considered valid if 'rules' are empty
     */
    public function __construct(int|string $key, mixed $value, array $params = [])
    {
        parent::__construct($key, $value, $params);

        if (isset($params[self::RULES])) {
            $this->rules = $params[self::RULES];
        }

        if (isset($params[self::JSON])) {
            $this->decode = (bool)$params[self::JSON];
        }

        $this->messages += [
            self::INVALID_VALUE => '%key%: %value% is not in a collection',
        ];
    }

    public function validate(): int
    {
        $run = parent::validate();

        if ($run !== self::CHECK) {
            return $run;
        }

        return $this->rules === [] ? self::VALID : $this->isValid();
    }

    public function getValue(): mixed
    {
        // don't change the value on error or if it is not empty
        if ($this->value || $this->error) {
            return $this->value;
        }

        return [];
    }

    protected function isValid(): int
    {
        $this->error = '';

        $collection = $this->decode ? json_decode($this->value, true) : $this->value;

        if (!is_array($collection)) {
            return $this->setAndReturnError(self::INVALID_VALUE);
        }

        $validatedContext = [];
        // Apply each rule to all data keys
        foreach ($this->rules as $rule) {
            $class = $this->resolve($rule);
            // $k is the key of the collection, normally the index
            foreach ($collection as $k => $data) {
                $class->setValue($data[$class->getKey()] ?? null);
                if ($class->validate() === RuleInterface::ERROR) {
                    $this->error = $class->getError();
                    return RuleInterface::ERROR;
                }
                $validatedContext[$k][$class->getKey()] = $class->getValue();
            }
        }

        $this->value = $validatedContext;

        return RuleInterface::VALID;
    }

    protected function resolve(array $rule): RuleInterface
    {
        // The first element must be the key context
        $key = $rule[0];
        // The second element must be the class validator name
        $class = $rule[1];

        return new $class($key, null, $rule);
    }

    /**
     * Empty value is null or [] only.
     *
     * @return bool
     */
    protected function isEmpty(): bool
    {
        return $this->value === null || $this->value === [];
    }
}
