<?php

declare(strict_types = 1);

namespace Elie\Validator\Rule;

use PHPUnit\Framework\TestCase;

class IpRuleTest extends TestCase
{

    /**
     * @dataProvider getIpValueProvider
     */
    public function testValidate($value, $params, $expectedResult, $expectedError): void
    {
        $rule = new IpRule('IP', $value, $params);

        $res = $rule->validate();

        assertThat($res, identicalTo($expectedResult));

        assertThat($rule->getError(), identicalTo($expectedError));
    }

    public function getIpValueProvider(): \Generator
    {
        yield 'Given value could be empty' => [
            '', [], IpRule::VALID, ''
        ];

        yield 'Given ipv4 0.0.0.0 should be valid' => [
            '0.0.0.0', [], IpRule::VALID, ''
        ];

        yield 'Given ipv4 10.0.0.0 should be valid' => [
            '10.0.0.0', [], IpRule::VALID, ''
        ];

        yield 'Given ipv4 172.16.10.0 should be valid' => [
            '172.16.10.0', [], IpRule::VALID, ''
        ];

        yield 'Given ipv4 192.168.2.10 should be valid' => [
            '192.168.2.10', [], IpRule::VALID, ''
        ];

        yield 'Given ipv4 255.255.255.255 should be valid' => [
            '255.255.255.255', [], IpRule::VALID, ''
        ];

        yield 'Given ipv4 255.255.253.0 should be valid' => [
            '255.255.253.0', [], IpRule::VALID, ''
        ];

        yield 'Given ipv6 0:0:0:0:0:0:0:0 should be valid' => [
            '0:0:0:0:0:0:0:0', [IpRule::FLAG => FILTER_FLAG_IPV6], IpRule::VALID, ''
        ];

        yield 'Given ipv6 ::255.255.255.255 should be valid' => [
            '::255.255.255.255', [IpRule::FLAG => FILTER_FLAG_IPV6], IpRule::VALID, ''
        ];

        yield 'Given ipv6 fe80:0000:0000:0000:0202:b3ff:fe1e:8329 should be valid' => [
            'fe80:0000:0000:0000:0202:b3ff:fe1e:8329', [IpRule::FLAG => FILTER_FLAG_IPV6], IpRule::VALID, ''
        ];

        yield 'Given ipv6 2001:0db8:85a3:0000:0000:8a2e:0.0.0.0 should be valid' => [
            '2001:0db8:85a3:0000:0000:8a2e:0.0.0.0', [IpRule::FLAG => FILTER_FLAG_IPV6], IpRule::VALID, ''
        ];

        yield 'Given 0 should not be valid' => [
            '0', [], IpRule::ERROR, 'IP: 0 is not a valid IP'
        ];

        yield 'Given 0.0.foo should not be valid' => [
            '0.0.foo', [], IpRule::ERROR, 'IP: 0.0.foo is not a valid IP'
        ];

        yield 'Given 255.255.255.255 should not be valid' => [
            '255.255.255.255', [IpRule::FLAG => 5], IpRule::ERROR, 'Filter IP flag: 5 is not valid'
        ];
    }
}
