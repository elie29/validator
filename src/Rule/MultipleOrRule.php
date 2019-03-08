<?php

declare(strict_types = 1);

namespace Elie\Validator\Rule;

/**
 * This class acts as a decorator to validate a key among provided rules.
 */
class MultipleOrRule extends MultipleAndRule
{

    protected function isValid(): int
    {
        $errors = [];

        foreach ($this->rules as $rule) {
            $class = $this->resolve($rule);

            // Key is valid on the first valid rule
            if ($class->validate() === RuleInterface::VALID) {
                // remove errors when at least a validation occurred
                $this->error = '';
                return RuleInterface::VALID;
            }

            // keep track on all errors
            $errors[] = $class->getError();
        }

        $this->error = implode("\n", $errors);

        return RuleInterface::ERROR;
    }
}
