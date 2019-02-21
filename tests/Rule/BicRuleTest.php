<?php

declare(strict_types = 1);

namespace Elie\Validator\Rule;

use PHPUnit\Framework\TestCase;

class BicRuleTest extends TestCase
{

    /**
     * @dataProvider getBicValueProvider
     */
    public function testValidate($value, $expectedResult, $expectedError): void
    {
        $rule = new BicRule('name', $value);

        $res = $rule->validate();

        assertThat($res, identicalTo($expectedResult));

        assertThat($rule->getError(), identicalTo($expectedError));
    }

    public function getBicValueProvider(): \Generator
    {
        yield 'Given value could be empty' => [
            '', RuleInterface::VALID, ''
        ];

        yield 'Given value ASPKAT2LXXX should be valid' => [
            'ASPKAT2LXXX', RuleInterface::VALID, ''
        ];

        yield 'Given value ASPKAT2L should be valid' => [
            'ASPKAT2L', RuleInterface::VALID, ''
        ];

        yield 'Given value DSBACNBXSHA should be valid' => [
            'DSBACNBXSHA', RuleInterface::VALID, ''
        ];

        yield 'Given value UNCRIT2B912 should be valid' => [
            'UNCRIT2B912', RuleInterface::VALID, ''
        ];

        yield 'Given value DABADKKK should be valid' => [
            'DABADKKK', RuleInterface::VALID, ''
        ];

        yield 'Given value RZOOAT2L303 should be valid' => [
            'RZOOAT2L303', RuleInterface::VALID, ''
        ];

        yield 'Given value ASPKAT2LXX should not be valid' => [
            'ASPKAT2LXX', RuleInterface::ERROR, 'name: ASPKAT2LXX has an invalid length'
        ];

        yield 'Given value ASPKAT2LX should not be valid' => [
            'ASPKAT2LX', RuleInterface::ERROR, 'name: ASPKAT2LX has an invalid length'
        ];

        yield 'Given value ASPKAT2LXXX1 should not be valid' => [
            'ASPKAT2LXXX1', RuleInterface::ERROR, 'name: ASPKAT2LXXX1 has an invalid length'
        ];

        yield 'Given value DABADKK should not be valid' => [
            'DABADKK', RuleInterface::ERROR, 'name: DABADKK has an invalid length'
        ];

        yield 'Given value RZ00AT2L303 should not be valid' => [
            'RZ00AT2L303', RuleInterface::ERROR, 'name: RZ00AT2L303 has an invalid bank code'
        ];

        yield 'Given value 1SBACNBXSHA should not be valid' => [
            '1SBACNBXSHA', RuleInterface::ERROR, 'name: 1SBACNBXSHA has an invalid bank code'
        ];

        yield 'Given value DSBA5NBXSHA should not be valid' => [
            'DSBA5NBXSHA', RuleInterface::ERROR, 'name: DSBA5NBXSHA has an invalid country code'
        ];

        yield 'Given value Dsba5nbxshA should not be valid' => [
            'Dsba5nbxshA', RuleInterface::ERROR, 'name: Dsba5nbxshA should be uppercase'
        ];
    }
}
