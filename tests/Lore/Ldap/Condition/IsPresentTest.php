<?php

namespace Lore\Ldap\Condition;

class IsPresentTest extends \Lore\BaseTest
{
    public function testAssemble()
    {
        $object = new IsPresent('mail');

        $expected = '(mail=*)';
        $this->assertEquals($expected, $object->assemble());
    }
}
