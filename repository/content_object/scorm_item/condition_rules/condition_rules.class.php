<?php
/**
 * $Id: condition_rules.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.scorm_item.condition_rules
 */
require_once dirname(__FILE__) . '/condition_rule.class.php';

class ConditionRules
{
    private $precondition_rules;
    private $postcondition_rules;
    private $exitcondition_rules;

    /**
     * Constructor
     */
    function ConditionRules()
    {
        $this->precondition_rules = array();
        $this->postcondition_rules = array();
        $this->exitcondition_rules = array();
    }

    function get_precondition_rules()
    {
        return $this->precondition_rules;
    }

    function get_postcondition_rules()
    {
        return $this->postcondition_rules;
    }

    function get_exitcondition_rules()
    {
        return $this->exitcondition_rules;
    }

    function set_precondition_rules($precondition_rules)
    {
        $this->precondition_rules = $precondition_rules;
    }

    function set_postcondition_rules($postcondition_rules)
    {
        $this->postcondition_rules = $postcondition_rules;
    }

    function set_exitcondition_rules($exitcondition_rules)
    {
        $this->exitcondition_rules = $exitcondition_rules;
    }

    function add_condition_rule($condition_rule, $type)
    {
        switch ($type)
        {
            case 'pre' :
                $this->precondition_rules[] = $condition_rule;
                break;
            case 'post' :
                $this->postcondition_rules[] = $condition_rule;
                break;
            case 'exit' :
                $this->exitcondition_rules[] = $condition_rule;
                break;
            default :
                $this->precondition_rules[] = $condition_rule;
                break;
        }
    }
}
?>