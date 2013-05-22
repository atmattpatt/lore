<?php

namespace Lore\Ldap;

class AttributeTest extends \Lore\BaseTest
{
    /**
     * Object under test
     * @var \Lore\Ldap\Attribute
     */
    protected $object;

    protected function setUp()
    {
        parent::setUp();

        $this->object = new Attribute();
    }

    public function testConstructor()
    {
        $expected = array(
            0 => 'Apple',
            1 => 'Banana',
            2 => 'Grapefruit',
        );

        $object = new Attribute($expected);

        $this->assertAttributeEquals($expected, 'values', $object);
        $this->assertAttributeEquals($expected, 'originalValues', $object);
    }

    public function testAddValue()
    {
        $expected = array(
            0 => 'Foo',
            1 => 'Bar',
        );

        $this->object->addValue('Foo');
        $this->object->addValue('Bar');

        $this->assertAttributeEquals($expected, 'values', $this->object);
    }

    public function testAddValueIfNotExists()
    {
        $expected = array(
            0 => 'Foo',
            1 => 'Bar',
        );

        $this->object->addValueIfNotExists('Foo');
        $this->object->addValueIfNotExists('Foo');
        $this->object->addValueIfNotExists('Bar');
        $this->object->addValueIfNotExists('Bar');

        $this->assertAttributeEquals($expected, 'values', $this->object);
    }

    public function testDeleteValue()
    {
        $input = array(
            'Apple',
            'Banana',
            'Banana',
            'Orange',
            'Grapefruit',
            'Banana',
        );
        $expected = array(
            0 => 'Apple',
            1 => 'Orange',
            2 => 'Grapefruit',
        );

        $this->setInternal($this->object, 'values', $input);

        $this->object->deleteValue('Banana');

        $this->assertAttributeEquals($expected, 'values', $this->object);
    }

    public function testGetIterator()
    {
        $this->assertInstanceOf('\Iterator', $this->object->getIterator());
    }

    public function testGetValue()
    {
        $this->object->addValue('Apple');
        $this->object->addValue('Banana');
        $this->object->addValue('Orange');

        $this->assertEquals('Apple', $this->object->getValue());
    }

    public function testHasValue()
    {
        $this->object->addValue('Apple');
        $this->object->addValue('Banana');
        $this->object->addValue('Orange');

        $this->assertTrue($this->object->hasValue('Banana'));
        $this->assertFalse($this->object->hasValue('Grapefruit'));
    }

    public function testOffsetExists()
    {
        $input = array(
            0 => 'Apple',
            1 => 'Banana',
            2 => 'Grapefruit',
        );

        $this->setInternal($this->object, 'values', $input);

        $this->assertTrue($this->object->offsetExists(1));
        $this->assertFalse($this->object->offsetExists(5));
    }

    public function testOffsetGet()
    {
        $input = array(
            0 => 'Apple',
            1 => 'Banana',
            2 => 'Grapefruit',
        );

        $this->setInternal($this->object, 'values', $input);

        $this->assertEquals('Banana', $this->object->offsetGet(1));
    }

    public function testOffsetSet()
    {
        $expected = array(
            2 => 'Grapefruit',
        );

        $this->object->offsetSet(2, 'Grapefruit');

        $this->assertAttributeEquals($expected, 'values', $this->object);
    }

    public function testOffsetUnset()
    {
        $input = array(
            0 => 'Apple',
            1 => 'Banana',
            2 => 'Orange',
            3 => 'Grapefruit',
        );
        $expected = array(
            0 => 'Apple',
            1 => 'Banana',
            3 => 'Grapefruit',
        );

        $this->setInternal($this->object, 'values', $input);

        $this->object->offsetUnset(2);

        $this->assertAttributeEquals($expected, 'values', $this->object);
    }

    public function testToArray()
    {
        $expected = array(
            0 => 'Apple',
            1 => 'Banana',
            2 => 'Grapefruit',
        );

        $this->object->addValue('Apple');
        $this->object->addValue('Banana');
        $this->object->addValue('Grapefruit');

        $this->assertEquals($expected, $this->object->toArray());
    }

    public function testToString()
    {
        $this->object->addValue('Apple');
        $this->object->addValue('Banana');
        $this->object->addValue('Orange');

        $this->assertEquals('Apple', (string)$this->object);
    }
}
