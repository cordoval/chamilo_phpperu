<?php
/**
 * $Id: in_condition.class.php 128 2009-11-09 13:13:20Z vanpouckesven $
 * @package common.condition
 */
require_once dirname(__FILE__) . '/condition.class.php';
/**
 * This class represents a selection condition that requires a value to be
 * present in a list of values.
 * An example of an instance would be a condition that requires that the ID of a
 * learning object be in the list {4,10,12}.
 *
 * @author Bart Mollet
 */
class InCondition implements Condition
{
    /**
     * Name
     */
    private $name;

    /**
     * Values
     */
    private $values;

    /**
     * Storage unit
     */
    private $storage_unit;

    /**
     * Is the storage unit name already an alias?
     */
    private $is_alias;

    /**
     * Constructor
     * @param string $name
     * @param array $values
     */
    function InCondition($name, $values, $storage_unit = null, $is_alias = false)
    {
        $this->name = $name;
        $this->values = $values;
        $this->storage_unit = $storage_unit;
        $this->is_alias = $is_alias;
    }

    /**
     * Gets the name
     * @return string
     */
    function get_name()
    {
        return $this->name;
    }

    /**
     * Gets the values
     * @return array
     */
    function get_values()
    {
        return $this->values;
    }

    /**
     * Gets the storage unit
     * @return string
     */
    function get_storage_unit()
    {
        return $this->storage_unit;
    }

    /**
     * Is the storage unit already an alias?
     * @return boolean
     */
    function is_alias()
    {
        return $this->is_alias;
    }

    /**
     * Gets a string representation of this condition
     * @return string
     */
    function __toString()
    {
        $values = $this->get_values();

        if (! is_array($values))
        {
            $values = array($values);
        }

        if (count($values) > 0)
        {
            return $this->get_name() . ' IN (' . implode(',', $values) . ')';
        }
        else
        {
            return '';
        }
    }
}
?>