<?php
/**
 * $Id: and_condition.class.php 128 2009-11-09 13:13:20Z vanpouckesven $
 * @package common.condition
 */
require_once dirname(__FILE__) . '/multiple_aggregate_condition.class.php';
/**
 * This type of condition requires that all of its aggregated conditions be met.
 * @author Tim De Pauw
 */
class AndCondition extends MultipleAggregateCondition
{
    const OPERATOR = ' AND ';

    /**
     * Gets a string representation of this condition
     * @return string
     */
    function __toString()
    {
        $conditions = $this->get_conditions();
        foreach ($conditions as $index => $condition)
        {
            $cond_string[] = '(' . $condition->__toString() . ')';
        }
        return implode($this->get_operator(), $cond_string);
    }

    function get_operator()
    {
        return self :: OPERATOR;
    }
}
?>