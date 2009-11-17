<?php
/**
 * $Id: multiple_aggregate_condition.class.php 128 2009-11-09 13:13:20Z vanpouckesven $
 * @package common.condition
 */
require_once dirname(__FILE__) . '/aggregate_condition.class.php';
/**
 *	This class represents a condition that consists of multiple aggregated
 *	conditions. Thus, it is used to model a single relationship (AND, OR
 *	and perhaps others) between its aggregated conditions.
 *
 *	@author Tim De Pauw
 */
abstract class MultipleAggregateCondition extends AggregateCondition
{
    /**
     * The aggregated conditions
     */
    private $conditions;

    /**
     * Constructor.
     * @param mixed $conditions The aggregated conditions, as either a list
     *                          or an array of Condition objects.
     */
    function MultipleAggregateCondition($conditions)
    {
        $this->conditions = (is_array($conditions) ? $conditions : func_get_args());
    }

    /**
     * Gets the aggregated conditions
     * @return array
     */
    function get_conditions()
    {
        return $this->conditions;
    }
}
?>