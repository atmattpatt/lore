<?php

namespace Lore\Ldap;

class QueryTest extends \Lore\BaseTest
{
    /**
     * Object under test
     * @var \Lore\Ldap\Query
     */
    protected $object;

    protected function setUp()
    {
        parent::setUp();

        $connection = $this->getMock('\Lore\Ldap\Connection');
        $this->object = new Query($connection);
    }

    public function testConstructor()
    {
        $connection = $this->getMock('\Lore\Ldap\Connection');
        $object = new Query($connection);

        $this->assertAttributeSame($connection, 'link', $object);
    }

    public function testWhere()
    {
        $condition = $this->getMock('\Lore\Ldap\Condition\QueryConditionInterface');
        $this->object->where($condition);

        $this->assertAttributeSame($condition, 'condition', $this->object);
    }

    public function testAndWhere()
    {
        $condition1 = $this->getMock('\Lore\Ldap\Condition\QueryConditionInterface');
        $condition2 = $this->getMock('\Lore\Ldap\Condition\QueryConditionInterface');
        $this->object->where($condition1)->andWhere($condition2);

        $this->assertAttributeInstanceOf('\Lore\Ldap\Condition\LogicalAnd', 'condition', $this->object);

        $logicalAnd = $this->getInternal($this->object, 'condition');

        $this->assertAttributeContains($condition1, 'conditions', $logicalAnd);
        $this->assertAttributeContains($condition2, 'conditions', $logicalAnd);
    }

    public function testOrWhere()
    {
        $condition1 = $this->getMock('\Lore\Ldap\Condition\QueryConditionInterface');
        $condition2 = $this->getMock('\Lore\Ldap\Condition\QueryConditionInterface');
        $this->object->where($condition1)->orWhere($condition2);

        $this->assertAttributeInstanceOf('\Lore\Ldap\Condition\LogicalOr', 'condition', $this->object);

        $logicalOr = $this->getInternal($this->object, 'condition');

        $this->assertAttributeContains($condition1, 'conditions', $logicalOr);
        $this->assertAttributeContains($condition2, 'conditions', $logicalOr);
    }

    public function testAllOf()
    {
        $conditions = array(
            $this->getMock('\Lore\Ldap\Condition\QueryConditionInterface'),
            $this->getMock('\Lore\Ldap\Condition\QueryConditionInterface'),
        );

        $allOf = Query::allOf($conditions);

        $this->assertInstanceOf('\Lore\Ldap\Condition\LogicalAnd', $allOf);
        $this->assertAttributeSame($conditions, 'conditions', $allOf);
    }

    public function testAnyOf()
    {
        $conditions = array(
            $this->getMock('\Lore\Ldap\Condition\QueryConditionInterface'),
            $this->getMock('\Lore\Ldap\Condition\QueryConditionInterface'),
        );

        $anyOf = Query::anyOf($conditions);

        $this->assertInstanceOf('\Lore\Ldap\Condition\LogicalOr', $anyOf);
        $this->assertAttributeSame($conditions, 'conditions', $anyOf);
    }

    public function testNot()
    {
        $condition = $this->getMock('\Lore\Ldap\Condition\QueryConditionInterface');

        $not = Query::not($condition);

        $this->assertInstanceOf('\Lore\Ldap\Condition\LogicalNot', $not);
        $this->assertAttributeSame($condition, 'condition', $not);
    }

    public function testEquals()
    {
        $attribute = 'objectClass';
        $criteria = 'inetOrgPerson';

        $condition = Query::equals($attribute, $criteria);

        $this->assertInstanceOf('\Lore\Ldap\Condition\EqualTo', $condition);
        $this->assertAttributeEquals($attribute, 'attribute', $condition);
        $this->assertAttributeEquals($criteria, 'criteria', $condition);
    }

    public function testNotEquals()
    {
        $attribute = 'objectClass';
        $criteria = 'inetOrgPerson';

        $condition = Query::notEquals($attribute, $criteria);

        $this->assertInstanceOf('\Lore\Ldap\Condition\LogicalNot', $condition);

        $not = $this->getInternal($condition, 'condition');

        $this->assertInstanceOf('\Lore\Ldap\Condition\EqualTo', $not);
        $this->assertAttributeEquals($attribute, 'attribute', $not);
        $this->assertAttributeEquals($criteria, 'criteria', $not);
    }

    public function testExists()
    {
        $attribute = 'mail';

        $condition = Query::exists($attribute);

        $this->assertInstanceOf('\Lore\Ldap\Condition\IsPresent', $condition);
        $this->assertAttributeEquals($attribute, 'attribute', $condition);
    }

    public function testNotExists()
    {
        $attribute = 'mail';

        $condition = Query::notExists($attribute);

        $this->assertInstanceOf('\Lore\Ldap\Condition\LogicalNot', $condition);

        $not = $this->getInternal($condition, 'condition');

        $this->assertInstanceOf('\Lore\Ldap\Condition\IsPresent', $not);
        $this->assertAttributeEquals($attribute, 'attribute', $not);
    }

    public function testGreaterThan()
    {
        $attribute = 'age';
        $criteria = '21';

        $condition = Query::greaterThan($attribute, $criteria);

        $this->assertInstanceOf('\Lore\Ldap\Condition\LogicalNot', $condition);

        $not = $this->getInternal($condition, 'condition');

        $this->assertInstanceOf('\Lore\Ldap\Condition\LessThanOrEqualTo', $not);
        $this->assertAttributeEquals($attribute, 'attribute', $not);
        $this->assertAttributeEquals($criteria, 'criteria', $not);
    }

    public function testGreaterThanOrEquals()
    {
        $attribute = 'age';
        $criteria = '21';

        $condition = Query::greaterThanOrEquals($attribute, $criteria);

        $this->assertInstanceOf('\Lore\Ldap\Condition\GreaterThanOrEqualTo', $condition);
        $this->assertAttributeEquals($attribute, 'attribute', $condition);
        $this->assertAttributeEquals($criteria, 'criteria', $condition);
    }

    public function testLessThan()
    {
        $attribute = 'age';
        $criteria = '65';

        $condition = Query::lessThan($attribute, $criteria);

        $this->assertInstanceOf('\Lore\Ldap\Condition\LogicalNot', $condition);

        $not = $this->getInternal($condition, 'condition');

        $this->assertInstanceOf('\Lore\Ldap\Condition\GreaterThanOrEqualTo', $not);
        $this->assertAttributeEquals($attribute, 'attribute', $not);
        $this->assertAttributeEquals($criteria, 'criteria', $not);
    }

    public function testLessThanOrEquals()
    {
        $attribute = 'age';
        $criteria = '65';

        $condition = Query::lessThanOrEquals($attribute, $criteria);

        $this->assertInstanceOf('\Lore\Ldap\Condition\LessThanOrEqualTo', $condition);
        $this->assertAttributeEquals($attribute, 'attribute', $condition);
        $this->assertAttributeEquals($criteria, 'criteria', $condition);
    }

    public function testLike()
    {
        $attribute = 'givenName';
        $criteria = 'John';

        $condition = Query::like($attribute, $criteria);

        $this->assertInstanceOf('\Lore\Ldap\Condition\ProximityTo', $condition);
        $this->assertAttributeEquals($attribute, 'attribute', $condition);
        $this->assertAttributeEquals($criteria, 'criteria', $condition);
    }

    public function testNotLike()
    {
        $attribute = 'givenName';
        $criteria = 'John';

        $condition = Query::notLike($attribute, $criteria);

        $this->assertInstanceOf('\Lore\Ldap\Condition\LogicalNot', $condition);

        $not = $this->getInternal($condition, 'condition');

        $this->assertInstanceOf('\Lore\Ldap\Condition\ProximityTo', $not);
        $this->assertAttributeEquals($attribute, 'attribute', $not);
        $this->assertAttributeEquals($criteria, 'criteria', $not);
    }
}