<?php

namespace Lore\Ldap;

class Ldap
{

    const LDAP_PORT = 389;
    const LDAPS_PORT = 636;

    public static function connect($host, $port = self::LDAP_PORT)
    {
        return new Connection($host, $port);
    }
}
