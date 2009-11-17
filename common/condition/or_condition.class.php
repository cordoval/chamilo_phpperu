<?php
/**
 * $Id: or_condition.class.php 128 2009-11-09 13:13:20Z vanpouckesven $
 * @package common.condition
 */
require_once dirname(__FILE__) . '/multiple_aggregate_condition.class.php';
/**
 *	This type of condition requires that one or more of its aggregated
 *	conditions be met.
 *
 *	@author Tim De Pauw
 */
class OrCondition extends MultipleAggregateCondition
{
    const OPERATOR = ' OR ';

    /**
     * Gets a string representation of this condition
     * @return string
     */
    function __toString()
    {
        $cond_string = array();
        $conditions = $this->get_conditions();
        
        foreach ($conditions as $condition)
        {
            $condition_string = $condition->__toString();
            
            if (! empty($condition_string))
            {
                $cond_string[] = '(' . $condition_string . ')';
            }
        }
        
        return implode($this->get_operator(), $cond_string);
    }

    function get_operator()
    {
        return self :: OPERATOR;
    }
}
?>