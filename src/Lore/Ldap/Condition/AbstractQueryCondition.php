<?php

namespace Lore\Ldap\Condition;

abstract class AbstractQueryCondition implements QueryConditionInterface
{

    /**
     * Escapes special characters
     *
     * @param string $string
     * @return string
     */
    protected function escape($string)
    {
        $replace = array(
            '&'  => '\26',
            '('  => '\28',
            ')'  => '\29',
            '|'  => '\7c',
            '='  => '\3d',
            '>'  => '\3e',
            '<'  => '\3c',
            '~'  => '\7e',
            '**' => '\2a',
            '/'  => '\2f',
            '\\' => '\5c',
        );

        return strtr($string, $replace);
    }
}
