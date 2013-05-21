<?php

namespace Lore\Ldap;

class ConnectionTest extends \Lore\BaseTest
{
    public function testConstructor()
    {
        $host = 'foobar.com';
        $port = 389;

        $connection = $this->getMock('\Lore\Ldap\Connection', array('open'));
        $connection->expects($this->once())
            ->method('open')
            ->with($this->equalTo($host), $this->equalTo($port));

        $connection->__construct($host, $port);
    }

    public function testBind()
    {
        $object = new Connection();

        $bindDn = 'john.smith';
        $passwd = 'Password1';

        $bind = $this->getMockFunction('ldap_bind', $object);
        $bind->expects($this->once())
            ->with($this->anything(), $this->equalTo($bindDn), $this->equalTo($passwd))
            ->will($this->returnValue(true));

        $object->bind($bindDn, $passwd);

        $this->assertTrue($object->isBound());
    }

    /**
     * @expectedException \Lore\Ldap\Exception\LdapException
     * @expectedExceptionMessage Could not bind
     */
    public function testBindFailure()
    {
        $object = new Connection();

        $bindDn = 'john.smith';
        $passwd = 'Password1';

        $bind = $this->getMockFunction('ldap_bind', $object);
        $bind->expects($this->once())
            ->with($this->anything(), $this->equalTo($bindDn), $this->equalTo($passwd))
            ->will($this->returnValue(false));

        $error = $this->getMockFunction('ldap_error', $object);
        $error->expects($this->once())
            ->will($this->returnValue('Error message'));

        $errno = $this->getMockFunction('ldap_errno', $object);
        $errno->expects($this->once())
            ->will($this->returnValue(999));

        $object->bind($bindDn, $passwd);
    }

    public function testClose()
    {
        $object = $this->getMock('\Lore\Ldap\Connection', array('unbind'));
        $object->expects($this->once())
            ->method('unbind');

        $object->close();
    }

    public function testGetError()
    {
        $object = new Connection();

        $expected = 'Insert generic error message here';

        $error = $this->getMockFunction('ldap_error', $object);
        $error->expects($this->once())
            ->will($this->returnValue($expected));

        $this->assertEquals($expected, $object->getError());
    }

    public function testGetErrorCode()
    {
        $object = new Connection();

        $expected = 999;

        $error = $this->getMockFunction('ldap_errno', $object);
        $error->expects($this->once())
            ->will($this->returnValue($expected));

        $this->assertEquals($expected, $object->getErrorCode());
    }

    public function testOpen()
    {
        $object = new Connection();
        $host = 'foobar.com';
        $port = 389;

        $open = $this->getMockFunction('ldap_connect', $object);
        $open->expects($this->once())
            ->with($host, $port)
            ->will($this->returnValue('resource'));

        $object->open($host, $port);

        $this->assertAttributeEquals('resource', 'link', $object);
    }

    /**
     * @expectedException \Lore\Ldap\Exception\LdapException
     * @expectedExceptionMessage Could not connect to host foobar.com:389
     */
    public function testOpenFailure()
    {
        $object = new Connection();
        $host = 'foobar.com';
        $port = 389;

        $open = $this->getMockFunction('ldap_connect', $object);
        $open->expects($this->once())
            ->with($host, $port)
            ->will($this->returnValue(false));

        $object->open($host, $port);
    }

    public function testStartTls()
    {
        $object = new Connection();

        $starttls = $this->getMockFunction('ldap_start_tls', $object);
        $starttls->expects($this->once())
            ->will($this->returnValue(1));

        $object->startTls();
    }

    /**
     * @expectedException \Lore\Ldap\Exception\LdapException
     * @expectedExceptionMessage Could not start TLS
     */
    public function testStartTlsFailure()
    {
        $object = new Connection();

        $starttls = $this->getMockFunction('ldap_start_tls', $object);
        $starttls->expects($this->once())
            ->will($this->returnValue(false));

        $error = $this->getMockFunction('ldap_error', $object);
        $error->expects($this->once())
            ->will($this->returnValue('Error message'));

        $errno = $this->getMockFunction('ldap_errno', $object);
        $errno->expects($this->once())
            ->will($this->returnValue(999));

        $object->startTls();
    }

    public function testUnbind()
    {
        $object = new Connection();

        $unbind = $this->getMockFunction('ldap_unbind', $object);
        $unbind->expects($this->once())
            ->will($this->returnValue(true));

        $object->unbind();

        $this->assertFalse($object->isBound());
    }

    /**
     * @expectedException \Lore\Ldap\Exception\LdapException
     * @expectedExceptionMessage Could not unbind
     */
    public function testUnbindFailure()
    {
        $object = new Connection();

        $unbind = $this->getMockFunction('ldap_unbind', $object);
        $unbind->expects($this->once())
            ->will($this->returnValue(false));

        $error = $this->getMockFunction('ldap_error', $object);
        $error->expects($this->once())
            ->will($this->returnValue('Error message'));

        $errno = $this->getMockFunction('ldap_errno', $object);
        $errno->expects($this->once())
            ->will($this->returnValue(999));

        $object->unbind();
    }

    public function testIsBound()
    {
        $object = new Connection();

        $this->setInternal($object, 'bound', true);
        $this->assertTrue($object->isBound());

        $this->setInternal($object, 'bound', false);
        $this->assertFalse($object->isBound());
    }
}
