<?php

namespace Lore\Ldap\Condition;

class LogicalAndTest extends \Lore\BaseTest
{
    public function testConstructor()
    {
        $expected = array(
            $this->getMockForAbstractClass('\Lore\Ldap\Condition\QueryConditionInterface')
        );

        $object = new LogicalAnd($expected);

        $this->assertAttributeEquals($expected, 'conditions', $object);
    }

    public function testAssemble()
    {
        $cond1 = $this->getMockForAbstractClass('\Lore\Ldap\Condition\QueryConditionInterface');
        $cond1->expects($this->once())
            ->method('assemble')
            ->will($this->returnValue('(objectClass=inetOrgPerson)'));

        $cond2 = $this->getMockForAbstractClass('\Lore\Ldap\Condition\QueryConditionInterface');
        $cond2->expects($this->once())
            ->method('assemble')
            ->will($this->returnValue('(mail=john@smith.com)'));

        $object = new LogicalAnd(array($cond1, $cond2));

        $expected = '(&(objectClass=inetOrgPerson)(mail=john@smith.com))';
        $this->assertEquals($expected, $object->assemble());
    }

    public function testAssembleEmpty()
    {
        $object = new LogicalAnd(array());

        $this->assertEmpty($object->assemble());
    }
}
