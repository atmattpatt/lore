<?php

namespace Lore;

abstract class BaseTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Test set up method
     *
     * Verifies that the runkit extension is loaded
     */
    protected function setUp()
    {
        if (!extension_loaded('runkit')) {
            $this->markTestSkipped('Extension runkit not available');
        }
    }

    /**
     * Shortcut to mock a function
     *
     * @param string $functionName
     * @param object $scopeObject
     * @return \PHPUnit_Extensions_MockFunction
     */
    protected function getMockFunction($functionName, $scopeObject)
    {
        return new \PHPUnit_Extensions_MockFunction($functionName, $scopeObject);
    }

    /**
     * Sets the value of an internal class property
     *
     * @param object $object
     * @param string $attribute
     * @param mixed $value
     */
    protected function setInternal($object, $attribute, $value)
    {
        $property = new \ReflectionProperty(get_class($object), $attribute);
        $property->setAccessible(true);
        $property->setValue($object, $value);
    }
}
