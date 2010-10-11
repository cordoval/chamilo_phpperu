<?php
/**
 * $Id: inequality_condition.class.php 128 2009-11-09 13:13:20Z vanpouckesven $
 * @package common.condition
 */
require_once dirname(__FILE__) . '/condition.class.php';
/**
 * This class represents a condition that requires an inequality. An example
 * would be requiring that a number be greater than 4.
 *
 * @author Tim De Pauw
 */
class InequalityCondition implements Condition
{
    /**
     * Constant defining "<"
     */
    const LESS_THAN = 1;

    /**
     * Constant defining "<="
     */
    const LESS_THAN_OR_EQUAL = 2;

    /**
     * Constant defining ">"
     */
    const GREATER_THAN = 3;

    /**
     * Constant defining ">="
     */
    const GREATER_THAN_OR_EQUAL = 4;

    /**
     * Name
     */
    private $name;

    /**
     * Operator
     */
    private $operator;

    /**
     * Value
     */
    private $value;

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
     * @param int $operator (LESS_THAN, LESS_THAN_OR_EQUAL, GREATER_THAN,
     * GREATER_THAN_OR_EQUAL)
     */
    function InequalityCondition($name, $operator, $value, $storage_unit = null, $is_alias = false)
    {
        $this->name = $name;
        $this->operator = $operator;
        $this->value = $value;
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
     * Gets the operator
     * @return int
     */
    function get_operator()
    {
        return $this->operator;
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
     * Is the storage unit already an alias?
     * @return boolean
     */
    function is_alias()
    {
        return $this->is_alias;
    }
}
?>