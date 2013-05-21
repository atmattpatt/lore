<?php

namespace Lore\Ldap\Condition;

interface QueryConditionInterface
{
    /**
     * Assembles the query condition into an LDAP filter string
     *
     * @return string
     */
    public function assemble();
}
