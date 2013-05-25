<?php

namespace Lore\Ldap;

class ResultSet implements \Iterator
{

    /**
     * Link identifier for LDAP connection
     * @var resource
     */
    protected $link;

    /**
     * Search result identifier
     * @var resource
     */
    protected $result;

    /**
     * The current result entity
     * @var type
     */
    protected $current = null;

    /**
     * The current position in the result set
     * @var int
     */
    protected $position = 0;

    /**
     * The number of result entries (null until lazy-loaded)
     * @var int|null
     */
    protected $count = null;

    public function __construct(Connection $link, $result)
    {
        $this->link = $link;
        $this->result = $result;
    }

    public function count()
    {
        if ($this->count === null) {
            $this->count = @ldap_count_entries($this->link->getLink(), $this->result);
            if ($this->count === false) {
                throw new Exception\LdapException(
                    'Could not count search result entries: ' . $link->getError(),
                    $link->getErrorCode()
                );
            }
        }

        return $this->count;
    }

    public function current()
    {
        if ($this->current === null) {
            $this->current = $this->getEntity(@ldap_next_entry($this->link->getLink(), $this->result));
        }

        return $this->current;
    }

    public function key()
    {
        return $this->position;
    }

    public function next()
    {
        $this->position++;
        if ($this->valid()) {
            $this->current = $this->getEntity(@ldap_next_entry($this->link->getLink(), $this->result));
        }
    }

    public function rewind()
    {
        $this->current = $this->getEntity(@ldap_first_entry($this->link->getLink(), $this->result));
        $this->position = 0;
    }

    public function valid()
    {
        return ($this->position >= 0 && $this->position < $this->count());
    }

    public function toArray()
    {
        $output = array();
        foreach ($this as $result) {
            $output[] = $result->toArray();
        }

        return $output;
    }

    public function serialize()
    {
        return $this->toArray();
    }

    public function unserialize($serialized)
    {
        throw new \BadMethodCallException('Cannot unserialize LDAP result set');
    }

    protected function getEntity($entry)
    {
        $entity = new Entity();

        $dn = ldap_get_dn($this->link->getLink(), $entry);
        $entity->setDn($dn);

        $attr = ldap_first_attribute($this->link->getLink(), $entry);
        $values = ldap_get_values($this->link->getLink(), $entry, $attr);
        unset($values['count']);

        $entity->addAttribute($attr, $values);

        while ($attr = ldap_next_attribute($this->link->getLink(), $entry)) {
            $values = ldap_get_values($this->link->getLink(), $entry, $attr);
            unset($values['count']);

            $entity->addAttribute($attr, $values);
        }

        $entity->setLoaded();

        return $entity;
    }
}
