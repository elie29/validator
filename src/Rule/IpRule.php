<?php

declare(strict_types = 1);

namespace Elie\Validator\Rule;

/**
 * This class verifies that a value is a valid IP.
 */
class IpRule extends AbstractRule
{

    /**#@+
     * Specific message error code
     */
    public const INVALID_IP = 'invalidIP';
    public const INVALID_IP_FLAG = 'invalidIPFlag';
    /**#@-*/

    /**#@+
     * Specific options for IpRule
     */
    public const TRIM = 'trim';
    public const FLAG = 'flag';
    /**#@-*/

    /**
     * flag range. default to FILTER_FLAG_IPV4
     */
    protected $flag = FILTER_FLAG_IPV4;

    /**
     * Params could have the following structure:
     * [
     *   'required' => {bool:optional},
     *   'trim' => {bool:optional},
     *   'messages' => {array:optional:key/value message patterns},
     *   'flag' => {int:optional:FILTER_FLAG_IPV4 by default}
     * ]
     */
    public function __construct(string $key, $value, array $params = [])
    {
        parent::__construct($key, $value, $params);

        if (isset($params[$this::FLAG])) {
            $this->flag = (int) $params[$this::FLAG];
        }

        $this->messages = $this->messages + [
            $this::INVALID_IP => '%key%: %value% is not a valid IP',
            $this::INVALID_IP_FLAG => 'Filter IP flag: %flag% is not valid',
        ];
    }

    public function validate(): int
    {
        $run = parent::validate();

        if ($run !== $this::CHECK) {
            return $run;
        }

        if ($this->isValidFlag() && $this->isValidIp()) {
            return $this::VALID;
        }

        return $this::ERROR;
    }

    protected function isValidFlag(): bool
    {
        $all = FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE;

        // invalid flag?
        if (($all & $this->flag) !== $this->flag || $this->flag === (FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6)) {
            $this->setAndReturnError($this::INVALID_IP_FLAG, [
                '%flag%' => $this->flag
            ]);
            return false;
        }

        return true;
    }

    protected function isValidIp(): bool
    {
        if (filter_var($this->value, FILTER_VALIDATE_IP, $this->flag) === false) {
            $this->setAndReturnError($this::INVALID_IP);
            return false;
        }

        return true;
    }
}
