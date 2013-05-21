<?php

namespace Lore;

abstract class BaseTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Invokes an internal class method
     *
     * @param object $object
     * @param string $name
     * @param mixed $args
     * @return mixed
     */
    protected function invokeInternal($object, $name, $args = array())
    {
        if (func_num_args() > 3 || !is_array($args)) {
            $args = array_splice(func_get_args(), 2);
        }

        $method = new \ReflectionMethod(get_class($object), $name);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $args);
    }

    /**
     * Sets the value of an internal class property
     *
     * @param object $object
     * @param string $name
     * @param mixed $value
     */
    protected function setInternal($object, $name, $value)
    {
        $property = new \ReflectionProperty(get_class($object), $name);
        $property->setAccessible(true);
        $property->setValue($object, $value);
    }

    /**
     * Gets the value of an internal class property
     *
     * @param object $object
     * @param string $name
     * @return mixed
     */
    protected function getInternal($object, $name)
    {
        $property = new \ReflectionProperty(get_class($object), $name);
        $property->setAccessible(true);

        return $property->getValue($object);
    }
}
