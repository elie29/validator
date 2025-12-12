<?php

declare(strict_types=1);

namespace Elie\Validator\Rule;

/**
 * This class verifies that given values is among a given list.
 * An empty value could be null or []
 */
class ChoicesRule extends AbstractRule
{

    /**
     * Specific message error code
     */
    public const INVALID_ITEM = 'INVALID_ITEM';

    /**
     * Specific options for ChoicesRule
     */
    public const LIST = 'list';

    /**
     * List of authorized values.
     * Default sets to an empty array.
     */
    protected array $list = [];

    /**
     * Params could have the following structure:
     * <code>
     * [
     *   'required' => {bool:optional},
     *   'messages' => {array:optional:key/value message patterns},
     *   'list' => {array:optional:empty array by default}
     * ]
     * </code>
     */
    public function __construct(int|string $key, mixed $value, array $params = [])
    {
        parent::__construct($key, $value, $params);

        if (isset($params[self::LIST])) {
            $this->list = $params[self::LIST];
        }

        $this->messages += [
            self::INVALID_ITEM => '%key%: %item% is not in the given list : %list%',
        ];
    }

    public function validate(): int
    {
        $run = parent::validate();

        if ($run !== $this::CHECK) {
            return $run;
        }

        foreach ($this->value as $item) {
            if (!in_array($item, $this->list, true)) {
                return $this->setAndReturnError(self::INVALID_ITEM, [
                    '%item%' => $item,
                    '%list%' => $this->stringify($this->list),
                ]);
            }
        }

        return RuleInterface::VALID;
    }

    /**
     * Empty value is null or [] only.
     *
     * @return bool
     */
    protected function isEmpty(): bool
    {
        return $this->value === null || $this->value === [];
    }
}
