<?php
/**
 * $Id: like_condition.class.php 128 2009-11-09 13:13:20Z vanpouckesven $
 * @package common.condition
 */
require_once dirname(__FILE__) . '/condition.class.php';
/**
 *	This class represents a selection condition that requires a likeness.
 *	An example of an instance would be a condition that requires that the ID
 *	of a Object be the like the number 4 e.g. 44, 412, 514, etc.
 *
 *  @author Hans De Bisschop
 *  @author Dieter De Neef
 */
class LikeCondition implements Condition
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
    function LikeCondition($name, $value, $storage_unit = null)
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
     * Gets a string representation of this condition
     * @return string
     */
    function __toString()
    {
        return $this->get_name() . ' LIKE \'%' . $this->get_value() . '%\'';
    }
}
?>