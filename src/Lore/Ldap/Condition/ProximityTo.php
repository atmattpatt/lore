<?php

namespace Lore\Ldap\Condition;

class ProximityTo extends ComparisonCondition
{

    /**
     * Assembles the query condition into an LDAP filter string
     *
     * @return string
     */
    public function assemble()
    {
        return '(' . $this->attribute . '~=' . $this->escape($this->criteria) . ')';
    }
}
