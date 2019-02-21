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

        return $this->setErrorCode();
    }

    protected function setErrorCode(): int
    {
        $methodsValidation = [
            'invalidLimit',
            'invalidUppercase',
            'invalidAlphaNumeric',
            'invalidBankCode',
            'invalidCountryCode'
        ];

        foreach ($methodsValidation as $method) {
            $codeError = $this->$method();
            if ($codeError !== null) {
                return $this->setAndReturnError($codeError);
            }
        }

        return RuleInterface::VALID;
    }

    protected function invalidLimit(): ?string
    {
        return in_array(strlen($this->value), [8, 11]) ? null : RuleInterface::INVALID_BIC_LIMIT;
    }

    protected function invalidUppercase(): ?string
    {
        return strtoupper($this->value) === $this->value ? null : RuleInterface::INVALID_BIC_UPPER;
    }

    protected function invalidAlphaNumeric(): ?string
    {
        return ctype_alnum($this->value) ? null : RuleInterface::INVALID_BIC_ALNUM;
    }

    protected function invalidBankCode(): ?string
    {
        return ctype_alpha(substr($this->value, 0, 4)) ? null : RuleInterface::INVALID_BIC_BC;
    }

    protected function invalidCountryCode(): ?string
    {
        return ctype_alpha(substr($this->value, 4, 2)) ? null : RuleInterface::INVALID_BIC_CC;
    }
}
