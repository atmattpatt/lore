<?php

namespace Lore\Ldap;

class Entity
{

    /**
     * An array of attributes currently assigned to the entity
     * @var array
     */
    private $attributes = array();

    /**
     * An array of attributes that have been added to the entity since it was
     * loaded; required to compute units of work
     * @var array
     */
    private $addedAttributes = array();

    /**
     * An array of attributes that have been deleted from the entity since it
     * was loaded; required to compute units of work
     * @var array
     */
    private $deletedAttributes = array();

    /**
     * Whether or not the entity is loaded
     * @var boolean
     */
    private $loaded = false;

    /**
     * Adds a new attribute to the entity
     *
     * @param string $attribute
     * @param mixed $value
     * @return \Lore\Ldap\Entity
     * @throws \Lore\Ldap\Exception\LdapException
     */
    public function addAttribute($attribute, $value)
    {
        // Cast value to array
        if (!is_array($value)) {
            $value = array($value);
        }

        if (isset($this->deletedAttributes[$attribute])) {
            // We are reinstating a previously-deleted attribute

            $this->attributes[$attribute] = $this->deletedAttributes[$attribute];
            unset($this->deletedAttributes[$attribute]);

            $this->replaceAttribute($attribute, $value);

        } elseif (!isset($this->attributes[$attribute]) && !isset($this->addedAttributes[$attribute])) {
            // We are creating a new attribute

            $this->attributes[$attribute] = new Attribute($value);

            if ($this->loaded) {
                $this->addedAttributes[$attribute] = $this->attributes[$attribute];
            }

        } else {
            // The attribute is already added
            throw new Exception\LdapException(sprintf('Cannot add attribute %s; attribute already exists', $attribute));
        }

        return $this;
    }

    /**
     * Deletes an existing attribute from the entity
     *
     * @param string $attribute
     * @return \Lore\Ldap\Entity
     */
    public function deleteAttribute($attribute)
    {
        if (isset($this->addedAttributes[$attribute])) {
            // The attribute was added after load
            unset($this->addedAttributes[$attribute]);
        } else {
            // The attribute must be deleted in the LDAP directory
            $this->deletedAttributes[$attribute] = $this->attributes[$attribute];
        }

        unset($this->attributes[$attribute]);

        return $this;
    }

    /**
     * Replaces the existing values of an attribute with a new value or array of
     * values.  If the attribute doesn't exist, it is added.
     *
     * @param string $attribute
     * @param mixed $value
     * @return \Lore\Ldap\Entity
     */
    public function replaceAttribute($attribute, $value)
    {
        // Cast value to array
        if (!is_array($value)) {
            $value = array($value);
        }

        if (!isset($this->attributes[$attribute])) {
            // The attribute doesn't yet exist
            $this->attributes[$attribute] = new Attribute($value);

            if ($this->loaded) {
                $this->addedAttributes[$attribute] = $this->attributes[$attribute];
            }
        } else {
            // The attribute already exists, so we must merge the values
            foreach ($value as $newValue) {
                $this->attributes[$attribute]->addValueIfNotExists($newValue);
            }

            foreach ($this->attributes[$attribute] as $offset => $oldValue) {
                if (!in_array($oldValue, $value)) {
                    $this->attributes[$attribute]->deleteValue($oldValue);
                }
            }
        }

        return $this;
    }

    /**
     * Magic method to access an LDAP attribute as a class property
     *
     * If the attribute doesn't exist, it will automatically be created without
     * any values.
     *
     * @param string $attribute
     * @return \Lore\Ldap\Attribute
     */
    public function __get($attribute)
    {
        if (!isset($this->attributes[$attribute])) {
            $this->addAttribute($attribute, array());
        }

        return $this->attributes[$attribute];
    }

    /**
     * Magic method to check for the existence of an LDAP attribute as a class
     * property
     *
     * @param string $attribute
     * @return boolean
     */
    public function __isset($attribute)
    {
        return isset($this->attributes[$attribute]);
    }

    /**
     * Magic method to set the value of an LDAP attribute as a class property
     *
     * @param string $attribute
     * @param mixed $value
     */
    public function __set($attribute, $value)
    {
        $this->replaceAttribute($attribute, $value);
    }

    /**
     * Magic method to delete an LDAP attribute as a class property
     *
     * @param string $attribute
     */
    public function __unset($attribute)
    {
        $this->deleteAttribute($attribute);
    }

    /**
     * Registers the entity as loaded
     *
     * Once setLoaded() is called, attribute additions/deletions are tracked to
     * compute a unit of work
     *
     * @return \Lore\Ldap\Attribute
     */
    public function setLoaded()
    {
        $this->loaded = true;

        return $this;
    }

    /**
     * Gets all attributes and all of their values as a multi-dimensional array
     *
     * @return array
     */
    public function toArray()
    {
        $output = array();
        foreach ($this->attributes as $attribute => $values) {
            $output[$attribute] = $values->toArray();
        }

        return $output;
    }
}
