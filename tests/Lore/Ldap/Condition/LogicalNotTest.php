<?php

namespace Lore\Ldap\Condition;

class LogicalNotTest extends \Lore\BaseTest
{
    public function testConstructor()
    {
        $expected = $this->getMockForAbstractClass('\Lore\Ldap\Condition\QueryConditionInterface');

        $object = new LogicalNot($expected);

        $this->assertAttributeEquals($expected, 'condition', $object);
    }

    public function testAssemble()
    {
        $cond = $this->getMockForAbstractClass('\Lore\Ldap\Condition\QueryConditionInterface');
        $cond->expects($this->once())
            ->method('assemble')
            ->will($this->returnValue('(objectClass=inetOrgPerson)'));

        $object = new LogicalNot($cond);

        $expected = '(!(objectClass=inetOrgPerson))';
        $this->assertEquals($expected, $object->assemble());
    }
}
