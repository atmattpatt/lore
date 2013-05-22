<?php

namespace Lore\Ldap;

class Attribute implements \ArrayAccess, \IteratorAggregate
{

    /**
     * An array of values for this attribute
     * @var array
     */
    private $values = array();

    /**
     * An array of the original values for this attirbute; used for computing a
     * unit of work when persisting the attribute
     * @var array
     */
    private $originalValues = array();

    /**
     * Class constructor
     *
     * @param array $values
     */
    public function __construct(array $values = array())
    {
        $this->values = $values;
        $this->originalValues = $values;
    }

    /**
     * Adds an attribute value
     *
     * @param mixed $value
     * @return \Lore\Ldap\Attribute
     */
    public function addValue($value)
    {
        $this->values[] = $value;

        return $this;
    }

    /**
     * Adds an attirbute value if it doesn't already exist
     *
     * @param mixed $newValue
     * @return \Lore\Ldap\Attribute
     */
    public function addValueIfNotExists($newValue)
    {
        foreach ($this->values as $value) {
            if ($newValue == $value) {
                return $this;
            }
        }

        $this->addValue($newValue);

        return $this;
    }

    /**
     * Deletes a value from the attribute
     *
     * @param mixed $oldValue
     * @return \Lore\Ldap\Attribute
     */
    public function deleteValue($oldValue)
    {
        foreach ($this->values as $key => $value) {
            if ($oldValue == $value) {
                unset($this->values[$key]);
            }
        }

        // Re-key
        $this->values = array_values($this->values);

        return $this;
    }

    /**
     * Gets the Iterator for all of the attribute values
     *
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->values);
    }

    /**
     * Gets the first value of the attribute
     *
     * @return mixed
     */
    public function getValue()
    {
        return isset($this->values[0]) ? $this->values[0] : null;
    }

    /**
     * Checks to see if a given value exists among all of the attribute values
     *
     * @param int $value
     * @return boolean
     */
    public function hasValue($value)
    {
        return in_array($value, $this->values);
    }

    /**
     * Checks for the existence of some attribute value offset
     *
     * @param int $offset
     * @return boolean
     */
    public function offsetExists($offset)
    {
        return isset($this->values[$offset]);
    }

    /**
     * Gets an attribute value by its offset
     *
     * @param int $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->values[$offset];
    }

    /**
     * Sets an attribute value by its offset
     *
     * @param int $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        $this->values[$offset] = $value;
    }

    /**
     * Unsets an attribute value by its offset
     *
     * @param int $offset
     */
    public function offsetUnset($offset)
    {
        unset($this->values[$offset]);
    }

    /**
     * Gets all of the values as an array
     *
     * @return array
     */
    public function toArray()
    {
        return array_values($this->values);
    }

    /**
     * Gets the string value of the attribute, which is the first value
     *
     * @return string
     */
    public function __toString()
    {
        return (string)$this->getValue();
    }
}
