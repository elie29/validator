<?php

declare(strict_types = 1);

namespace Elie\Validator\Rule;

use Elie\Validator\Helper\Text;

/**
 * This class verifies that a value is a valid string.
 * It calls Text::removeInvisibleChars in order to clean the string
 * after validation.
 */
class StringCleanerRule extends StringRule
{

    public function getValue(): string
    {
        $value = parent::getValue();

        // better in case called before validate
        if (is_string($value)) {
            $value = Text::removeInvisibleChars($value);
        }

        return $value;
    }
}
