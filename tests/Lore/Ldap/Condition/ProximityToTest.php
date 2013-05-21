<?php

namespace Lore\Ldap\Condition;

class ProximityToTest extends \Lore\BaseTest
{
    public function testAssemble()
    {
        $object = new ProximityTo('givenName', 'John');

        $expected = '(givenName~=John)';
        $this->assertEquals($expected, $object->assemble());
    }
}
