<?php
/**
 * $Id: equality_condition.class.php 128 2009-11-09 13:13:20Z vanpouckesven $
 * @package common.condition
 */
require_once dirname(__FILE__) . '/condition.class.php';
/**
 *	This class represents a selection condition that requires an equality.
 *	An example of an instance would be a condition that requires that the ID
 *	of a learning object be the number 4.
 *
 *	@author Tim De Pauw
 */
class EqualityCondition implements Condition
{
    /**
     * Name
     */
    private $name;
    /**
     * Value
     */
    private $value;
    /**
     * Storage unit
     */
    private $storage_unit;

    /**
     * Constructor
     * @param string $name
     * @param string $value
     */
    function EqualityCondition($name, $value, $storage_unit = null)
    {
        $this->name = $name;
        $this->value = $value;
        $this->storage_unit = $storage_unit;
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
     * Gets the value
     * @return string
     */
    function get_value()
    {
        return $this->value;
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
     * Set the storage unit 
     * @param $storage_unit string
     * @return void
     */
    function set_storage_unit($storage_unit)
    {
        return $this->storage_unit = $storage_unit;
    }

    /**
     * Gets a string representation of this condition
     * @return string
     */
    function __toString()
    {
        $value = $this->get_value();
        
        if (is_null($value))
        {
            return $this->get_name() . ' IS NULL';
        }
        else
        {
            return $this->get_name() . ' = \'' . $this->get_value() . '\'';
        }
    }
}
?>