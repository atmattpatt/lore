<?php

namespace Lore\Ldap\Condition;

class GreaterThanOrEqualToTest extends \Lore\BaseTest
{
    public function testAssemble()
    {
        $object = new GreaterThanOrEqualTo('age', '21');

        $expected = '(age>=21)';
        $this->assertEquals($expected, $object->assemble());
    }
}
