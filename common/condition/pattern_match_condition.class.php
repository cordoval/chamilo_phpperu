<?php
/**
 * $Id: pattern_match_condition.class.php 128 2009-11-09 13:13:20Z vanpouckesven $
 * @package common.condition
 */
require_once dirname(__FILE__) . '/condition.class.php';
/**
 * This class represents a selection condition that uses a pattern for matching.
 * An example of an instance would be a condition that requires that the title
 * of a learning object containts the word "math". The pattern 	is case
 * insensitive and supports two types of wildcard characters: an 	asterisk (*)
 * must match any sequence of characters, and a question mark 	(?) must match a
 * single character.
 *
 *	@author Tim De Pauw
 */
class PatternMatchCondition implements Condition
{
    /**
     * Name
     */
    private $name;
    /**
     * Pattern
     */
    private $pattern;
    /**
     * Storage unit
     */
    private $storage_unit;

    /**
     * Constructor
     * @param string $name
     * @param string $pattern
     */
    function PatternMatchCondition($name, $pattern, $storage_unit = null)
    {
        $this->name = $name;
        $this->pattern = $pattern;
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
     * Gets the storage unit
     * @return string
     */
    function get_storage_unit()
    {
        return $this->storage_unit;
    }

    /**
     * Gets the pattern
     * @return string
     */
    function get_pattern()
    {
        return $this->pattern;
    }

    /**
     * Gets a string representation of this condition
     * @return string
     */
    function __toString()
    {
        $result = $this->name . ' = \'' . $this->pattern . '\'';
        return $result;
    }
}
?>