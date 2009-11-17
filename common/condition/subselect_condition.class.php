<?php
/**
 * $Id: subselect_condition.class.php 128 2009-11-09 13:13:20Z vanpouckesven $
 * @package common.condition
 */
require_once dirname(__FILE__) . '/condition.class.php';
/**
 * This class represents a subselect condition
 *
 *	@author Sven Vanpoucke
 */
class SubselectCondition implements Condition
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
     * Table
     */
    private $storage_unit_value;
    
    /**
     * Condition
     */
    private $condition;
    
    /**
     * The table for the name
     */
    private $storage_unit_name;

    /**
     * Constructor
     * @param string $name
     * @param array $values
     */
    function SubselectCondition($name, $value, $storage_unit_value, $condition, $storage_unit_name)
    {
        $this->name = $name;
        $this->value = $value;
        $this->storage_unit_value = $storage_unit_value;
        $this->storage_unit_name = $storage_unit_name;
        $this->condition = $condition;
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
     * Gets the storage_unit name for this subselect condition
     * @return string
     */
    function get_storage_unit_value()
    {
        return $this->storage_unit_value;
    }

    /**
     * Gets the storage_unit name for the name
     * @return string
     */
    function get_storage_unit_name()
    {
        return $this->storage_unit_name;
    }

    /**
     * Gets the condition for the subselected storage_unit
     */
    function get_condition()
    {
        return $this->condition;
    }

    /**
     * Gets a string representation of this condition
     * @return string
     */
    function __toString()
    {
        if ($this->get_condition())
        {
            $where = ' WHERE ' . $this->get_condition();
        }
        
        return $this->get_name() . ' IN (SELECT ' . $this->get_value() . ' FROM ' . $this->get_storage_unit_value() . $where . ')';
    }
}
?>