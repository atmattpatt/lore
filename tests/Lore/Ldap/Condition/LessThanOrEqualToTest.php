<?php

namespace Lore\Ldap\Condition;

class LessThanOrEqualToTest extends \Lore\BaseTest
{
    public function testAssemble()
    {
        $object = new LessThanOrEqualTo('age', '65');

        $expected = '(age<=65)';
        $this->assertEquals($expected, $object->assemble());
    }
}
