<?php

namespace Lore\Ldap;

class Connection
{

    /**
     * Link identifier for LDAP connection
     * @var resource
     */
    protected $link;

    /**
     * Whether or not we are bound to the LDAP directory
     * @var boolean
     */
    protected $bound = false;

    /**
     * Class constructor
     *
     * Optionally opens a connection to the LDAP server
     *
     * @param string $host
     * @param int $port
     */
    public function __construct($host = null, $port = Ldap::LDAP_PORT)
    {
        if ($host !== null) {
            $this->open($host, $port);
        }
    }

    /**
     * Binds to the LDAP directory
     *
     * @param string $bindDn
     * @param string $password
     * @return \Lore\Ldap\Connection
     * @throws \Lore\Ldap\Exception\LdapException
     */
    public function bind($bindDn = null, $password = null)
    {
        $result = @ldap_bind($this->link, $bindDn, $password);

        if ($result === false) {
            throw new Exception\LdapException('Could not bind: ' . $this->getError(), $this->getErrorCode());
        }

        $this->bound = true;

        return $this;
    }

    /**
     * Alias for Connection::unbind()
     *
     * @return \Lore\Ldap\Connection
     */
    public function close()
    {
        $this->unbind();

        return $this;
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

    /**
     * Upgrades the connection to TLS
     *
     * @return \Lore\Ldap\Connection
     * @throws \Lore\Ldap\Exception\LdapException
     */
    public function startTls()
    {
        $result = @ldap_start_tls($this->link);

        if ($result === false) {
            throw new Exception\LdapException('Could not start TLS: ' . $this->getError(), $this->getErrorCode());
        }

        return $this;
    }

    /**
     * Unbinds from the LDAP directory
     *
     * @return \Lore\Ldap\Connection
     * @throws \Lore\Ldap\Exception\LdapException
     */
    public function unbind()
    {
        $result = @ldap_unbind($this->link);

        if ($result === false) {
            throw new Exception\LdapException('Could not unbind ' . $this->getError(), $this->getErrorCode());
        }

        $this->bound = false;

        return $this;
    }

    /**
     * Checks to see if we are bound to the LDAP directory
     *
     * @return boolean
     */
    public function isBound()
    {
        return $this->bound;
    }
}
