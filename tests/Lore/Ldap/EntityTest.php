<?php

namespace Lore\Ldap;

class EntityTest extends \Lore\BaseTest
{
    /**
     * Object under test
     * @var \Lore\Ldap\Entity
     */
    protected $object;

    protected function setUp()
    {
        parent::setUp();

        $this->object = new Entity();
    }

    public function testAddAttribute()
    {
        $expectedAttributes = array(
            'foo' => new Attribute(array('bar')),
            'bar' => new Attribute(array('baz')),
        );
        $expectedAddedAttributes = array(
            'bar' => new Attribute(array('baz')),
        );

        $this->object->addAttribute('foo', 'bar');
        $this->object->setLoaded();
        $this->object->addAttribute('bar', 'baz');

        $this->assertAttributeEquals($expectedAttributes, 'attributes', $this->object);
        $this->assertAttributeEquals($expectedAddedAttributes, 'addedAttributes', $this->object);
    }

    /**
     * @expectedException \Lore\Ldap\Exception\InvalidAttributeException
     * @expectedExceptionMessage Cannot add attribute dn
     */
    public function testAddAttributePreventsDnChange()
    {
        $this->object->addAttribute('dn', array());
    }

    public function testAddAttributePreviouslyDeleted()
    {
        $this->object->addAttribute('foo', 'bar');
        $this->object->setLoaded();
        $this->object->deleteAttribute('foo');
        $this->object->addAttribute('foo', 'baz');

        $this->assertEquals('baz', $this->object->foo->getValue());
        $this->assertAttributeEmpty('deletedAttributes', $this->object);
        $this->assertAttributeEmpty('addedAttributes', $this->object);
    }

    /**
     * @expectedException \Lore\Ldap\Exception\LdapException
     * @expectedExceptionMessage Cannot add attribute foo; attribute already exists
     */
    public function testAddAttributeAlreadyExists()
    {
        $this->object->addAttribute('foo', 'bar');
        $this->object->addAttribute('foo', 'baz');
    }

    public function testDeleteAttribute()
    {
        $expected = array(
            'foo' => new Attribute(array('baz')),
        );

        $this->object->addAttribute('foo', 'baz');
        $this->object->setLoaded();
        $this->object->addAttribute('bar', 'baz');

        $this->object->deleteAttribute('foo');
        $this->object->deleteAttribute('bar');

        $this->assertAttributeEmpty('attributes', $this->object);
        $this->assertAttributeEmpty('addedAttributes', $this->object);
        $this->assertAttributeEquals($expected, 'deletedAttributes', $this->object);
    }

    public function testReplaceAttribute()
    {
        $this->object->replaceAttribute('foo', 'apple');
        $this->object->setLoaded();
        $this->object->replaceAttribute('foo', 'orange');
        $this->object->replaceAttribute('bar', 'banana');
        $this->object->replaceAttribute('bar', array('banana', 'grapefruit'));

        $this->assertFalse($this->object->foo->hasValue('apple'));
        $this->assertTrue($this->object->foo->hasValue('orange'));
        $this->assertTrue($this->object->bar->hasValue('banana'));
        $this->assertTrue($this->object->bar->hasValue('grapefruit'));
    }

    public function testGet()
    {
        $foo1 = $this->object->__get('foo'); // Triggers addAttribute()
        $foo2 = $this->object->__get('foo');

        $this->assertSame($foo1, $foo2);
    }

    public function testIsSet()
    {
        $this->object->addAttribute('foo', 'bar');
        $this->assertTrue($this->object->__isset('foo'));

        $this->object->deleteAttribute('foo');
        $this->assertFalse($this->object->__isset('foo'));
    }

    public function testSet()
    {
        $object = $this->getMock('\Lore\Ldap\Entity', array('replaceAttribute'));
        $object->expects($this->once())
            ->method('replaceAttribute')
            ->with($this->equalTo('foo'), $this->equalTo('bar'));

        $object->__set('foo', 'bar');
    }

    public function testUnset()
    {
        $object = $this->getMock('\Lore\Ldap\Entity', array('deleteAttribute'));
        $object->expects($this->once())
            ->method('deleteAttribute')
            ->with($this->equalTo('foo'));

        $object->__unset('foo');
    }

    public function testSetLoaded()
    {
        $this->object->setLoaded();
        $this->assertAttributeEquals(true, 'loaded', $this->object);
    }

    public function testToArray()
    {
        $expected = array(
            'dn' => 'cn=nobody,dc=foobar,dc=com',
            'foo' => array(
                0 => 'apple',
                1 => 'banana',
            ),
            'bar' => array(
                0 => 'orange',
                1 => 'grapefruit'
            ),
        );

        $this->object->setDn('cn=nobody,dc=foobar,dc=com');
        $this->object->addAttribute('foo', array('apple', 'banana'));
        $this->object->addAttribute('bar', array('orange', 'grapefruit'));

        $this->assertEquals($expected, $this->object->toArray());
    }
}
