<?php
/**
 * $Id: condition_rule.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.scorm_item.condition_rules
 */
require_once dirname(__FILE__) . '/rule_condition.class.php';

class ConditionRule
{
    private $conditions;
    private $action;
    private $conditions_operator;

    function ConditionRule()
    {
        $this->conditions = array();
        $this->action = null;
        $this->conditions_operator = 'all';
    }

    function get_action()
    {
        return $this->action;
    }

    function get_conditions()
    {
        return $this->conditions;
    }

    function set_action($action)
    {
        $this->action = $action;
    }

    function set_conditions($conditions)
    {
        $this->conditions = $conditions;
    }

    function add_condition($condition)
    {
        $this->conditions[] = $condition;
    }

    function get_condition($index)
    {
        return $this->conditions[$index];
    }

    function get_conditions_operator()
    {
        return $this->conditions_operator;
    }

    function set_conditions_operator($conditions_operator)
    {
        $this->conditions_operator = $conditions_operator;
    }

}
?>