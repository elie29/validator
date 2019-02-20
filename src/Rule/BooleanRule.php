<?php

declare(strict_types = 1);

namespace Elie\Validator\Rule;

/**
 * This class verifies that a value is a valid boolean.
 */
class BooleanRule extends AbstractRule
{

    public function validate(): int
    {
        $run = parent::validate();

        if ($run !== RuleInterface::CHECK) {
            return $run;
        }

        if (! $this->isBool()) {
            $this->error = "{$this->key}: {$this->value} is not a valid boolean";
            return RuleInterface::ERROR;
        }

        return RuleInterface::VALID;
    }

    protected function isBool(): bool
    {
        $val = $this->value;
        return
            $val === 0 || $val === 1 ||
            $val === '0' || $val === '1' ||
            $val === true || $val === false;
    }
}
