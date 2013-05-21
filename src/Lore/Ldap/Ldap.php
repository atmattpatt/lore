<?php

namespace Lore\Ldap;

class Ldap
{

    const LDAP_PORT = 389;
    const LDAPS_PORT = 636;

    /**
     * Shortcut to instantiate an LDAP connection
     *
     * @param string $host
     * @param int $port
     * @return \Lore\Ldap\Connection
     */
    public static function connect($host, $port = self::LDAP_PORT)
    {
        return new Connection($host, $port);
    }
}
