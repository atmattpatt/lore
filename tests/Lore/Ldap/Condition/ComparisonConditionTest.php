<?php

namespace Lore\Ldap\Condition;

class ComparisonConditionTest extends \Lore\BaseTest
{
    public function testConstructor()
    {
        $expectedAttribute = 'objectClass';
        $expectedCriteria  = 'inetOrgPerson';

        $condition = $this->getMockBuilder('\Lore\Ldap\Condition\ComparisonCondition')
            ->setConstructorArgs(array($expectedAttribute, $expectedCriteria))
            ->getMock();

        $this->assertAttributeEquals($expectedAttribute, 'attribute', $condition);
        $this->assertAttributeEquals($expectedCriteria, 'criteria', $condition);
    }
}
