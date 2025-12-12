<?php

declare(strict_types=1);

namespace Elie\Validator\Rule;

/**
 * This class verifies that a value is a valid BIC.
 */
class BicRule extends AbstractRule
{

    /**#@+
     * Specific message error code
     */
    public const INVALID_BIC_LIMIT = 'invalidBicLimit';
    public const INVALID_BIC_UPPER = 'invalidBicUpper';
    public const INVALID_BIC_ALNUM = 'invalidBicAlnum';
    public const INVALID_BIC_BC = 'invalidBicBC';
    public const INVALID_BIC_CC = 'invalidBicCC';
    /**#@-*/

    /**
     * Specific option for BicRule
     */
    public const TRIM = 'trim';

    /**
     * Params could have the following structure:
     * <code>
     * [
     *   'required' => {bool:optional:false by default},
     *   'trim' => {bool:optional:true by default},
     *   'messages' => {array:optional:key/value message patterns}
     * ]
     * </code>
     */
    public function __construct(int|string $key, mixed $value, array $params = [])
    {
        parent::__construct($key, $value, $params);

        $this->messages += [
            $this::INVALID_BIC_LIMIT => '%key%: %value% has an invalid length',
            $this::INVALID_BIC_UPPER => '%key%: %value% should be uppercase',
            $this::INVALID_BIC_ALNUM => '%key%: %value% should be alphanumeric',
            $this::INVALID_BIC_BC => '%key%: %value% has an invalid bank code',
            $this::INVALID_BIC_CC => '%key%: %value% has an invalid country code',
        ];
    }

    public function validate(): int
    {
        $run = parent::validate();

        if ($run !== $this::CHECK) {
            return $run;
        }

        $this->value = str_replace([' ', '-'], '', $this->value);

        return $this->setErrorCode();
    }

    protected function setErrorCode(): int
    {
        $methodsValidation = [
            fn() => $this->invalidLimit(),
            fn() => $this->invalidUppercase(),
            fn() => $this->invalidAlphaNumeric(),
            fn() => $this->invalidBankCode(),
            fn() => $this->invalidCountryCode(),
        ];

        foreach ($methodsValidation as $method) {
            $codeError = $method();
            if ($codeError !== null) {
                return $this->setAndReturnError($codeError);
            }
        }

        return $this::VALID;
    }

    protected function invalidLimit(): ?string
    {
        return in_array(strlen($this->value), [8, 11]) ? null : $this::INVALID_BIC_LIMIT;
    }

    protected function invalidUppercase(): ?string
    {
        return strtoupper($this->value) === $this->value ? null : $this::INVALID_BIC_UPPER;
    }

    protected function invalidAlphaNumeric(): ?string
    {
        return ctype_alnum($this->value) ? null : $this::INVALID_BIC_ALNUM;
    }

    protected function invalidBankCode(): ?string
    {
        return ctype_alpha(substr($this->value, 0, 4)) ? null : $this::INVALID_BIC_BC;
    }

    protected function invalidCountryCode(): ?string
    {
        return ctype_alpha(substr($this->value, 4, 2)) ? null : $this::INVALID_BIC_CC;
    }
}
