<?php

declare(strict_types = 1);

namespace Elie\Validator\Rule;

/**
 * This class verifies that a value matches a given pattern.
 */
class MatchRule extends AbstractRule
{

    /**
     * Specific message error code
     */
    public const INVALID_PATTERN = 'invalidPattern';

    /**#@+
     * Specific options for MatchRule
     */
    public const TRIM = 'trim';
    public const PATTERN = 'pattern';
    /**#@-*/

    /**
     * A regular pattern string, e.g.:
     * /^[a-z]{2, 12}$/i
     * @var string
     */
    protected $pattern;

    /**
     * Params could have the following structure:
     * [
     *   'required' => {bool:optional:false by default},
     *   'trim' => {bool:optional:true by default},
     *   'messages' => {array:optional:key/value message patterns},
     *   'pattern' => {string:required}
     * ]
     */
    public function __construct($key, $value, array $params = [])
    {
        parent::__construct($key, $value, $params);

        $this->pattern = $params[$this::PATTERN];

        $this->messages = $this->messages + [
            $this::INVALID_PATTERN => '%key%: %value% does not match %pattern%',
        ];
    }

    public function validate(): int
    {
        $run = parent::validate();

        if ($run !== $this::CHECK) {
            return $run;
        }

        if (! preg_match($this->pattern, $this->value)) {
            return $this->setAndReturnError($this::INVALID_PATTERN, [
                '%pattern%' => $this->pattern
            ]);
        }

        return $this::VALID;
    }
}
