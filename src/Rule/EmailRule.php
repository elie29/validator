<?php

declare(strict_types = 1);

namespace Elie\Validator\Rule;

/**
 * This class verifies that a value is a valid email.
 */
class EmailRule extends AbstractRule
{

    public function validate(): int
    {
        $run = parent::validate();

        if ($run !== RuleInterface::CHECK) {
            return $run;
        }

        if (! $this->isValid()) {
            $this->error = "{$this->key}: {$this->value} is not a valid email";
            return RuleInterface::ERROR;
        }

        return RuleInterface::VALID;
    }

    protected function isValid(): bool
    {
        return filter_var($this->value, FILTER_VALIDATE_EMAIL) !== false;
    }
}
