<?php

namespace Lore\Ldap;

class IntegrationTest extends \Lore\BaseMockFunctionTest
{

    /**
     * @codeCoverageIgnore
     * @group Integration
     */
    public function testMain()
    {
        $connection = new Connection();
        $connection->open('ldap.orlando2018.com', Ldap::LDAP_PORT);

        //$connection->startTls();

        $connection->bind('uid=matthew.patterson,ou=People,dc=orlando2018,dc=com', 'zu&utHeg');

        $search = new Query($connection);
        $search
            ->searchBase('dc=orlando2018,dc=com')
            ->where($search->exists('mail'))
            ->andWhere($search->equals('sn', 'Patterson'));

        $result = $search->query();

        $result->rewind();

        ini_set('xdebug.var_display_max_depth', 5);
        var_dump($result->toArray());

        $connection->close();
    }
}
