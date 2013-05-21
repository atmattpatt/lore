<?php

namespace Lore\Ldap\Condition;

abstract class ComparisonCondition extends AbstractQueryCondition
{

    /**
     * The LDAP attribute
     * @var string
     */
    protected $attribute;

    /**
     * The search criteria
     * @var string
     */
    protected $criteria;

    /**
     * Class constructor
     *
     * @param string $attribute
     * @param string $criteria
     */
    public function __construct($attribute, $criteria)
    {
        $this->attribute = $attribute;
        $this->criteria  = $criteria;
    }
}
