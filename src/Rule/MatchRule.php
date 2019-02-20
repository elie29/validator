<?php

declare(strict_types = 1);

namespace Elie\Validator\Rule;

/**
 * This class verifies that a value matches a given pattern.
 */
class MatchRule extends AbstractRule
{

    /**
     * A regular pattern string, e.g.:
     * /^[a-z]{2, 12}$/i
     * @var string
     */
    protected $pattern;

    /**
     * Params could have the following structure:
     * [
     *   'required' => {bool:optional},
     *   'trim' => {bool:optional},
     *   'pattern' => {string:required}
     * ]
     */
    public function __construct(string $key, $value, array $params = [])
    {
        parent::__construct($key, $value, $params);

        $this->pattern = $params['pattern'];
    }

    public function validate(): int
    {
        $run = parent::validate();

        if ($run !== RuleInterface::CHECK) {
            return $run;
        }

        if (! preg_match($this->pattern, $this->value)) {
            $this->error = "{$this->key}: {$this->value} does not match {$this->pattern}";
            return RuleInterface::ERROR;
        }

        return RuleInterface::VALID;
    }
}
