<?php

declare(strict_types = 1);

namespace Elie\Validator\Rule;

/**
 * This class verifies that a value is a valid BIC.
 */
class BicRule extends AbstractRule
{

    public function validate(): int
    {
        $run = parent::validate();

        if ($run !== RuleInterface::CHECK) {
            return $run;
        }

        $this->value = str_replace([' ', '-'], '', $this->value);

        if (! $this->isValid()) {
            $this->error = "{$this->key}: {$this->value} is not a valid BIC";
            return RuleInterface::ERROR;
        }

        return RuleInterface::VALID;
    }

    protected function isValid(): bool
    {
        return $this->shouldBeLimited() && $this->shouldBeUppercase() &&
            $this->shouldBeAlphaNumeric() && $this->shouldHaveBankCode() &&
            $this->shouldHaveCountryCode();
    }

    protected function shouldBeLimited(): bool
    {
        return in_array(strlen($this->value), [8, 11]);
    }

    protected function shouldBeUppercase(): bool
    {
        return strtoupper($this->value) === $this->value;
    }

    protected function shouldBeAlphaNumeric(): bool
    {
        return ctype_alnum($this->value);
    }

    protected function shouldHaveBankCode(): bool
    {
        return ctype_alpha(substr($this->value, 0, 4));
    }

    protected function shouldHaveCountryCode(): bool
    {
        return ctype_alpha(substr($this->value, 4, 2));
    }
}
