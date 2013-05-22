<?php

namespace Lore\Ldap;

class QueryTest extends \Lore\BaseMockFunctionTest
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

    public function testAttribute()
    {
        $expected = array('givenName' => true);

        $this->object->attribute('givenName');
        $this->assertAttributeContains($expected, 'attributes', $this->object);

        $this->object->attribute('givenName', false);
        $this->assertAttributeNotContains($expected, 'attributes', $this->object);
    }

    public function testAttributesOnly()
    {
        $this->object->attributesOnly();
        $this->assertAttributeEquals(1, 'attributesOnly', $this->object);

        $this->object->attributesOnly(false);
        $this->assertAttributeEquals(0, 'attributesOnly', $this->object);
    }

    public function testLimit()
    {
        $expected = 20;

        $this->object->limit($expected);
        $this->assertAttributeEquals($expected, 'limit', $this->object);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid value -1 for LDAP query limit
     */
    public function testLimitInvalidArgument()
    {
        $this->object->limit(-1);
    }

    public function testSearchBase()
    {
        $expected = 'ou=People,dc=acme,dc=com';

        $this->object->searchBase($expected);
        $this->assertAttributeEquals($expected, 'base', $this->object);
    }

    public function testTimeout()
    {
        $expected = 60;

        $this->object->timeout($expected);
        $this->assertAttributeEquals($expected, 'timeout', $this->object);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid value -1 for LDAP query timeout
     */
    public function testTimeoutInvalidArgument()
    {
        $this->object->timeout(-1);
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

    public function testDereferenceNever()
    {
        $this->object->dereferenceNever();
        $this->assertAttributeEquals(LDAP_DEREF_NEVER, 'aliasDeref', $this->object);
    }

    public function testDereferenceSearching()
    {
        $this->object->dereferenceSearching();
        $this->assertAttributeEquals(LDAP_DEREF_SEARCHING, 'aliasDeref', $this->object);
    }

    public function testDereferenceFinding()
    {
        $this->object->dereferenceFinding();
        $this->assertAttributeEquals(LDAP_DEREF_FINDING, 'aliasDeref', $this->object);
    }

    public function testDereferenceAlways()
    {
        $this->object->dereferenceAlways();
        $this->assertAttributeEquals(LDAP_DEREF_ALWAYS, 'aliasDeref', $this->object);
    }

    public function testQuery()
    {
        $search = $this->getMockFunction('ldap_search', $this->object);
        $search->expects($this->once())
            ->with(
                $this->anything(),
                $this->equalTo('dc=foobar,dc=com'),
                $this->equalTo('(&(objectClass=inetOrgPerson)(mail=*))'),
                $this->equalTo(array('sn', 'givenName', 'mail')),
                $this->equalTo(0),
                $this->equalTo(100),
                $this->equalTo(60),
                $this->equalTo(LDAP_DEREF_ALWAYS)
            )
            ->will($this->returnValue('resource'));

        $this->object
            ->searchBase('dc=foobar,dc=com')
            ->where($this->object->equals('objectClass', 'inetOrgPerson'))
            ->andWhere($this->object->exists('mail'))
            ->attribute('sn')
            ->attribute('givenName')
            ->attribute('mail')
            ->limit(100)
            ->timeout(60)
            ->dereferenceAlways();

        $result = $this->object->query();

        $this->assertInstanceOf('\Lore\Ldap\ResultSet', $result);
        $this->assertAttributeSame($this->getInternal($this->object, 'link'), 'link', $result);
    }

    /**
     * @expectedException \Lore\Ldap\Exception\QueryException
     * @expectedExceptionMessage Search base DN is empty
     */
    public function testQueryNoBaseDn()
    {
        $this->object->searchBase('');
        $this->object->query();
    }

    /**
     * @expectedException \Lore\Ldap\Exception\QueryException
     * @expectedExceptionMessage LDAP query failed
     * @expectedExceptionCode 999
     */
    public function testQueryFailure()
    {
        $connection = $this->getMock('\Lore\Ldap\Connection', array('getError', 'getErrorCode'));
        $connection->expects($this->once())
            ->method('getError')
            ->will($this->returnValue('Test error'));
        $connection->expects($this->once())
            ->method('getErrorCode')
            ->will($this->returnValue(999));

        $object = new Query($connection);

        $search = $this->getMockFunction('ldap_search', $object);
        $search->expects($this->once())
            ->will($this->returnValue(false));

        $object->searchBase('dc=foobar,dc=com');
        $object->query();
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
