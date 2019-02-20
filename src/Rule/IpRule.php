<?php

declare(strict_types = 1);

namespace Elie\Validator\Rule;

/**
 * This class verifies that a value is a valid IP.
 */
class IpRule extends AbstractRule
{

    /**
     * flag range. default to FILTER_FLAG_IPV4
     */
    protected $flag = FILTER_FLAG_IPV4;

    /**
     * Params could have the following structure:
     * [
     *   'required' => {bool:optional},
     *   'trim' => {bool:optional},
     *   'flag' => {int:optional:FILTER_FLAG_IPV4 by default}
     * ]
     */
    public function __construct(string $key, $value, array $params = [])
    {
        parent::__construct($key, $value, $params);

        if (isset($params['flag'])) {
            $this->flag = (int) $params['flag'];
        }
    }

    public function validate(): int
    {
        $run = parent::validate();

        if ($run !== RuleInterface::CHECK) {
            return $run;
        }

        if ($this->isValidFlag() && $this->isValidIp()) {
            return RuleInterface::VALID;
        }

        return RuleInterface::ERROR;
    }

    protected function isValidFlag(): bool
    {
        $all = FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE;

        // accepted flag only
        if (($all & $this->flag) !== $this->flag || $this->flag === (FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6)) {
            $this->error = "Filter flag <{$this->flag}> is not valid";
            return false;
        }

        return true;
    }

    protected function isValidIp(): bool
    {
        if (filter_var($this->value, FILTER_VALIDATE_IP, $this->flag) === false) {
            $this->error = "{$this->key}: {$this->value} is not a valid IP";
            return false;
        }

        return true;
    }
}
