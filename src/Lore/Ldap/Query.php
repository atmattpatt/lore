<?php

namespace Lore\Ldap;

use InvalidArgumentException;
use Lore\Ldap\Condition\QueryConditionInterface;

class Query
{

    /**
     * Link identifier for LDAP connection
     * @var resource
     */
    protected $link;

    /**
     * An array of attributes to return
     * @var array
     */
    protected $attributes = array();

    /**
     * Whether or not to return attributes only
     * @var int
     */
    protected $attributesOnly = 0;

    /**
     * The search base DN
     * @var string
     */
    protected $base;

    /**
     * The current query condition
     * @var \Lore\Ldap\Condition\QueryConditionInterface|null
     */
    protected $condition = null;

    /**
     * How to handle aliases during the search
     * @var int
     */
    protected $aliasDeref = LDAP_DEREF_NEVER;

    /**
     * The maximum number of entries to fetch; 0 means no limit
     * @var int
     */
    protected $limit = 0;

    /**
     * The number of sends to spend searching; 0 means no timeout
     * @var int
     */
    protected $timeout = 0;

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
     * Sets whether or not an attribute should be returned in the search result
     *
     * @param string $attribute
     * @param boolean $required
     * @return \Lore\Ldap\Query
     */
    public function attribute($attribute, $required = true)
    {
        $this->attributes[$attribute] = ($required === true);

        return $this;
    }

    /**
     * Sets whether or not the return attributes types only instead of attribute
     * types and values
     *
     * @param boolean $attrsonly
     * @return \Lore\Ldap\Query
     */
    public function attributesOnly($attrsonly = true)
    {
        $this->attributesOnly = ($attrsonly === true) ? 1 : 0;

        return $this;
    }

    /**
     * Sets the maximum number of entries to fetch
     *
     * @param int $limit Zero means no limit
     * @return \Lore\Ldap\Query
     * @throws InvalidArgumentException
     */
    public function limit($limit = 0)
    {
        if ($limit < 0) {
            throw new InvalidArgumentException(
                sprintf(
                    'Invalid value %d for LDAP query limit; value must be at least zero',
                    $limit
                )
            );
        }

        $this->limit = $limit;

        return $this;
    }

    /**
     * Sets the base DN for the search
     *
     * @param string $base
     * @return \Lore\Ldap\Query
     */
    public function searchBase($base = '')
    {
        $this->base = $base;

        return $this;
    }

    /**
     * Sets the maximum number of seconds to spend searching
     *
     * @param int $timeout Zero means no limit
     * @return \Lore\Ldap\Query
     * @throws InvalidArgumentException
     */
    public function timeout($timeout = 0)
    {
        if ($timeout < 0) {
            throw new InvalidArgumentException(
                sprintf(
                    'Invalid value %d for LDAP query timeout; value must be at least zero',
                    $timeout
                )
            );
        }

        $this->timeout = $timeout;

        return $this;
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
     * Never dereference aliases
     *
     * @return \Lore\Ldap\Query
     */
    public function dereferenceNever()
    {
        $this->aliasDeref = LDAP_DEREF_NEVER;

        return $this;
    }

    /**
     * Dereference aliases during the search but not when locating the base object of the search
     *
     * @return \Lore\Ldap\Query
     */
    public function dereferenceSearching()
    {
        $this->aliasDeref = LDAP_DEREF_SEARCHING;

        return $this;
    }

    /**
     * Dereferences aliases when locating the base object but not during the search
     *
     * @return \Lore\Ldap\Query
     */
    public function dereferenceFinding()
    {
        $this->aliasDeref = LDAP_DEREF_FINDING;

        return $this;
    }

    /**
     * Always dereference aliases
     *
     * @return \Lore\Ldap\Query
     */
    public function dereferenceAlways()
    {
        $this->aliasDeref = LDAP_DEREF_ALWAYS;

        return $this;
    }

    public function query()
    {
        // Base DN is required
        if (strlen($this->base) == 0) {
            throw new Exception\QueryException('Search base DN is empty');
        }

        // Assemble filter
        $filter = '';
        if ($this->condition instanceof QueryConditionInterface) {
            $filter = $this->condition->assemble();
        }

        $attributes = array();
        foreach ($this->attributes as $attribute => $require) {
            if ($require) {
                $attributes[] = $attribute;
            }
        }

        $result = @ldap_search(
            $this->link->getLink(),
            $this->base,
            $filter,
            $attributes,
            $this->attributesOnly,
            $this->limit,
            $this->timeout,
            $this->aliasDeref
        );

        if ($result === false) {
            throw new Exception\QueryException(
                'LDAP query failed: ' . $this->link->getError(),
                $this->link->getErrorCode()
            );
        }

        return new ResultSet($this->link, $result);
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
