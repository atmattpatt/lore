<?php

namespace Lore\Ldap;

class LdapTest extends \Lore\BaseTest
{
    public function testConnect()
    {
        $connection = Ldap::connect(null);
        $this->assertInstanceOf('\Lore\Ldap\Connection', $connection);
    }
}
