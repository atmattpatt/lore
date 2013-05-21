<?php

namespace Lore\Ldap;

use Lore\Ldap\Condition\QueryConditionInterface;

class Query
{

    /**
     * Link identifier for LDAP connection
     * @var resource
     */
    protected $link;

    /**
     * The current query condition
     * @var \Lore\Ldap\Condition\QueryConditionInterface|null
     */
    protected $condition = null;

    /**
     * Class constructor
     *
     * @param \Lore\Ldap\Connection $link
     */
    public function __construct(Connection $link)
    {
        $this->link = $link;
    }

    /**
     * Sets the query condition
     *
     * @param \Lore\Ldap\Condition\QueryConditionInterface $condition
     * @return \Lore\Ldap\Query
     */
    public function where(QueryConditionInterface $condition)
    {
        $this->condition = $condition;

        return $this;
    }

    /**
     * Conjoins a new query condition with the current query condition
     *
     * @param \Lore\Ldap\Condition\QueryConditionInterface $condition
     * @return \Lore\Ldap\Query
     */
    public function andWhere(QueryConditionInterface $condition)
    {
        $this->condition = new Condition\LogicalAnd(array($this->condition, $condition));

        return $this;
    }

    /**
     * Disjoins a new query condition with the current query condition
     *
     * @param \Lore\Ldap\Condition\QueryConditionInterface $condition
     * @return \Lore\Ldap\Query
     */
    public function orWhere(QueryConditionInterface $condition)
    {
        $this->condition = new Condition\LogicalOr(array($this->condition, $condition));

        return $this;
    }

    /**
     * Shortcut to create a new LogicalAnd condition
     *
     * @param array $conditions
     * @return \Lore\Ldap\Condition\LogicalAnd
     */
    public static function allOf(array $conditions)
    {
        return new Condition\LogicalAnd($conditions);
    }

    /**
     * Shortcut to create a new LogicalOr condition
     *
     * @param array $conditions
     * @return \Lore\Ldap\Condition\LogicalOr
     */
    public static function anyOf(array $conditions)
    {
        return new Condition\LogicalOr($conditions);
    }

    /**
     * Shortcut to create a LogicalNot condition
     *
     * @param \Lore\Ldap\Condition\QueryConditionInterface $condition
     * @return \Lore\Ldap\Condition\LogicalNot
     */
    public static function not(QueryConditionInterface $condition)
    {
        return new Condition\LogicalNot($condition);
    }

    /**
     * Shortcut to create an EqualTo condition
     *
     * @param string $attribute
     * @param string $criteria
     * @return \Lore\Ldap\Condition\EqualTo
     */
    public static function equals($attribute, $criteria)
    {
        return new Condition\EqualTo($attribute, $criteria);
    }

    /**
     * Shortcut to create the negation of an EqualTo condition
     *
     * @param string $attribute
     * @param string $criteria
     * @return \Lore\Ldap\Condition\LogicalNot
     */
    public static function notEquals($attribute, $criteria)
    {
        return new Condition\LogicalNot(new Condition\EqualTo($attribute, $criteria));
    }

    /**
     * Shortcut to create an IsPresent condition
     *
     * @param string $attribute
     * @return \Lore\Ldap\Condition\IsPresent
     */
    public static function exists($attribute)
    {
        return new Condition\IsPresent($attribute);
    }

    /**
     * Shortcut to create the negation of an IsPresent condition
     *
     * @param string $attribute
     * @return \Lore\Ldap\Condition\LogicalNot
     */
    public static function notExists($attribute)
    {
        return new Condition\LogicalNot(new Condition\IsPresent($attribute));
    }

    /**
     * Shortcut to create a "greater than" condition, which is defined as the
     * negation of a LessThanOrEqualTo condition since no strict "greater than"
     * operator is defined by RFC 4511
     *
     * @param string $attribute
     * @param string $criteria
     * @return \Lore\Ldap\Condition\LogicalNot
     */
    public static function greaterThan($attribute, $criteria)
    {
        return new Condition\LogicalNot(new Condition\LessThanOrEqualTo($attribute, $criteria));
    }

    /**
     * Shortcut to create a GreaterThanOrEqualTo condition
     *
     * @param string $attribute
     * @param string $criteria
     * @return \Lore\Ldap\Condition\GreaterThanOrEqualTo
     */
    public static function greaterThanOrEquals($attribute, $criteria)
    {
        return new Condition\GreaterThanOrEqualTo($attribute, $criteria);
    }

    /**
     * Shortcut to create a "less than" condition, which is defined as the
     * negation of a GreaterThanOrEqualTo condition since no strict "less than"
     * operator is defined by RFC 4511
     *
     * @param string $attribute
     * @param string $criteria
     * @return \Lore\Ldap\Condition\LogicalNot
     */
    public static function lessThan($attribute, $criteria)
    {
        return new Condition\LogicalNot(new Condition\GreaterThanOrEqualTo($attribute, $criteria));
    }

    /**
     * Shortcut to create a LessThanOrEqualTo condition
     *
     * @param type $attribute
     * @param type $criteria
     * @return \Lore\Ldap\Condition\LessThanOrEqualTo
     */
    public static function lessThanOrEquals($attribute, $criteria)
    {
        return new Condition\LessThanOrEqualTo($attribute, $criteria);
    }

    /**
     * Shortcut to create a new ProximityTo condition
     *
     * @param string $attribute
     * @param string $criteria
     * @return \Lore\Ldap\Condition\ProximityTo
     */
    public static function like($attribute, $criteria)
    {
        return new Condition\ProximityTo($attribute, $criteria);
    }

    /**
     * Shortcut to create the negation of a ProximityTo condition
     *
     * @param string $attribute
     * @param string $criteria
     * @return \Lore\Ldap\Condition\LogicalNot
     */
    public static function notLike($attribute, $criteria)
    {
        return new Condition\LogicalNot(new Condition\ProximityTo($attribute, $criteria));
    }
}
