<?php
/**
 * $Id: not_condition.class.php 128 2009-11-09 13:13:20Z vanpouckesven $
 * @package common.condition
 */
require_once dirname(__FILE__) . '/aggregate_condition.class.php';
/**
 *	This type of aggregate condition negates a single condition, thus
 *	requiring that that condition not be met.
 *
 *	@author Tim De Pauw
 */
class NotCondition extends AggregateCondition
{
    /**
     * The condition to negate
     */
    private $condition;

    /**
     * Constructor
     * @param Condition $condition
     */
    function NotCondition($condition)
    {
        $this->condition = $condition;
    }

    /**
     * Gets the condition to negate
     * @return Condition
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
        return ' NOT (' . $this->get_condition() . ')';
    }
}
?>