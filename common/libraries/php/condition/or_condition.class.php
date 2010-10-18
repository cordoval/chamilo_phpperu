<?php
namespace common\libraries;
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

    function get_operator()
    {
        return self :: OPERATOR;
    }
}
?>