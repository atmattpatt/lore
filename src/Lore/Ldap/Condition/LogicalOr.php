<?php

namespace Lore\Ldap\Condition;

class LogicalOr extends AbstractQueryCondition
{

    /**
     * An array of query conditions to be disjoined
     * @var array
     */
    protected $conditions;

    /**
     * Class constructor
     *
     * @param array $conditions
     */
    public function __construct(array $conditions)
    {
        $this->conditions = $conditions;
    }

    /**
     * Assembles the query condition into an LDAP filter string
     *
     * @return string
     */
    public function assemble()
    {
        $assembled = '';
        foreach ($this->conditions as $condition) {
            if ($condition instanceof QueryConditionInterface) {
                $assembled .= $condition->assemble();
            }
        }

        if (strlen($assembled)) {
            return '(|' . $assembled . ')';
        }

        return '';
    }
}
