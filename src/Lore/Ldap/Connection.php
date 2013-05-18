<?php

namespace Lore\Ldap;

class Connection
{

    /**
     * Link identifier for LDAP connection
     * @var resource
     */
    protected $link;

    public function __construct($host = null, $port = Ldap::LDAP_PORT)
    {
        if ($host !== null) {
            $this->open($host, $port);
        }
    }

    public function bind()
    {

    }

    public function close()
    {

    }

    public function compare()
    {

    }

    /**
     * Gets the LDAP error message of the last command
     *
     * @return string
     */
    public function getError()
    {
        return ldap_error($this->link);
    }

    /**
     * Gets the LDAP error number of the last command
     *
     * @return int
     */
    public function getErrorCode()
    {
        return ldap_errno($this->link);
    }

    /**
     * Opens a new LDAP connection
     *
     * With OpenLDAP 2.x, ldap_connect() initilizes the connecting parameters but
     * does not actually establish a connection.
     *
     * @param string $host The hostname to connect to; if using OpenLDAP 2.x, a URL may also be used
     * @param int $port
     * @return \Lore\Ldap\Connection
     * @throws \Lore\Ldap\Exception\LdapException
     */
    public function open($host, $port = Ldap::LDAP_PORT)
    {
        $this->link = ldap_connect($host, $port);

        if ($this->link === false) {
            throw new Exception\LdapException(sprintf('Could not connect to host %s:%d', $host, $port));
        }

        return $this;
    }

    public function save()
    {

    }

    public function startTls()
    {
        $result = @ldap_start_tls($this->link);
        if ($result === false) {
            throw new Exception\LdapException('Could not start TLS: ' . $this->getError(), $this->getErrorCode());
        }
    }

    public function unbind()
    {

    }

    public function isBound()
    {

    }
}
