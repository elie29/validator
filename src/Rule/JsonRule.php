<?php

declare(strict_types = 1);

namespace Elie\Validator\Rule;

/**
 * This class verifies that a value is a valid json.
 */
class JsonRule extends AbstractRule
{

    public function validate(): int
    {
        $run = parent::validate();

        if ($run !== RuleInterface::CHECK) {
            return $run;
        }

        if (! $this->isValid()) {
            return $this->setAndReturnError(self::INVALID_JSON);
        }

        return RuleInterface::VALID;
    }

    protected function isValid(): bool
    {
        return json_decode($this->value) !== null;
    }
}
