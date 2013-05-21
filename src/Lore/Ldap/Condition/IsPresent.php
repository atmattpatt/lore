<?php

namespace Lore\Ldap\Condition;

class IsPresent extends AbstractQueryCondition
{

    /**
     * The LDAP attribute
     * @var string
     */
    protected $attribute;

    /**
     * Class constructor
     *
     * @param string $attribute
     */
    public function __construct($attribute)
    {
        $this->attribute = $attribute;
    }

    /**
     * Assembles the query condition into an LDAP filter string
     *
     * @return string
     */
    public function assemble()
    {
        return '(' . $this->attribute . '=*)';
    }
}
