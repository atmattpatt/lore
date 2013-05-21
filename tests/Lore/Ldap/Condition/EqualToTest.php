<?php

namespace Lore\Ldap\Condition;

class EqualToTest extends \Lore\BaseTest
{
    public function testAssemble()
    {
        $object = new EqualTo('objectClass', 'inetOrgPerson');

        $expected = '(objectClass=inetOrgPerson)';
        $this->assertEquals($expected, $object->assemble());
    }
}
