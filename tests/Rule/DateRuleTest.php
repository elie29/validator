<?php

declare(strict_types = 1);

namespace Elie\Validator\Rule;

use PHPUnit\Framework\TestCase;

class DateRuleTest extends TestCase
{

    public function testValidateWithSeparator(): void
    {
        $rule = new DateRule('date', '2017,22,03', [
            'format' => 'yyyy/dd/mm',
            'separator' => '[,/]'
        ]);

        $res = $rule->validate();

        assertThat($res, identicalTo(1));

        assertThat($rule->getError(), identicalTo(''));

        assertThat($rule->getSeparator(), equalTo('[,/]'));
    }

    /**
     * @dataProvider getDateValueProvider
     */
    public function testValidate($value, $params, $expectedResult, $expectedError): void
    {
        $rule = new DateRule('date', $value, $params);

        $res = $rule->validate();

        assertThat($res, identicalTo($expectedResult));

        assertThat($rule->getError(), identicalTo($expectedError));
    }

    public function getDateValueProvider(): \Generator
    {
        yield 'Given value could be empty' => [
            '', [], RuleInterface::VALID, ''
        ];

        yield 'Given value 12/03/2017 should be valid' => [
            '12/03/2017', ['format' => 'dd/mm/yyyy'], RuleInterface::VALID, ''
        ];

        yield 'Given value 12/03/17 should be valid' => [
            '12/03/17', ['format' => 'dd/mm/yy'], RuleInterface::VALID, ''
        ];

        yield 'Given value 29/02/2017 should not be valid' => [
            '29/02/2017', ['format' => 'dd/mm/yyyy'], RuleInterface::ERROR,
            "date: 29/02/2017 is not a valid date"
        ];

        yield 'Given value 0/02/2017 should not be valid' => [
            '0/02/2017', ['format' => 'dd/mm/yyyy'], RuleInterface::ERROR,
            "date: 0/02/2017 is not a valid date"
        ];

        yield 'Given value 0/02 should not be valid' => [
            '0/02', ['format' => 'dd/mm/yyyy'], RuleInterface::ERROR,
            "date: 0/02 is not a valid date"
        ];

        yield 'Given value separator . should not be valid' => [
            '12/02/2017', ['separator' => '.'], RuleInterface::ERROR,
            "date: 12/02/2017 does not have a valid format: array (  0 => 'dd/mm/yyyy',) or separator: ."
        ];

        yield 'Given value format dd/dd/mm should not be valid' => [
            '12/02/2017', ['format' => 'dd/dd/mm'], RuleInterface::ERROR,
            "date: 12/02/2017 does not have a valid format: array (  0 => 'dd/dd/mm',) or separator: [,-./]"
        ];

        yield 'Given value format dd/ss/mm should not be valid' => [
            '12/02/2017', ['format' => 'dd/ss/mm'], RuleInterface::ERROR,
            "date: 12/02/2017 does not have a valid format: array (  0 => 'dd/ss/mm',) or separator: [,-./]"
        ];

        yield 'Given value format dd/ss/mm should not be valid, with specific message' => [
            '12/02/2017', ['format' => 'dd/ss/mm', 'messages' => [
                RuleInterface::INVALID_DATE_FORMAT => '%key% has invalid format: %format%'
            ]],
            RuleInterface::ERROR, "date has invalid format: array (  0 => 'dd/ss/mm',)"
        ];
    }
}
