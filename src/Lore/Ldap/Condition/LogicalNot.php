<?php

namespace Lore\Ldap\Condition;

class LogicalNot extends AbstractQueryCondition
{

    /**
     * The condition to be negated
     * @var array
     */
    protected $condition;

    /**
     * Class constructor
     *
     * @param QueryConditionInterface $condition
     */
    public function __construct(QueryConditionInterface $condition)
    {
        $this->condition = $condition;
    }

    /**
     * Assembles the query condition into an LDAP filter string
     *
     * @return string
     */
    public function assemble()
    {
        return '(!' . $this->condition->assemble() . ')';
    }
}
