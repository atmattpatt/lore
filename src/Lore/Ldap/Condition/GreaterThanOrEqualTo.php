<?php

namespace Lore\Ldap\Condition;

class GreaterThanOrEqualTo extends ComparisonCondition
{

    /**
     * Assembles the query condition into an LDAP filter string
     *
     * @return string
     */
    public function assemble()
    {
        return '(' . $this->attribute . '>=' . $this->escape($this->criteria) . ')';
    }
}
