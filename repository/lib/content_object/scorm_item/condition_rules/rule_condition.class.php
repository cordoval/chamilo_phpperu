<?php
/**
 * $Id: rule_condition.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.scorm_item.condition_rules
 */
class RuleCondition
{
    private $condition;
    private $not_condition;
    private $referenced_objective;
    private $measure_treshold;

    function RuleCondition()
    {
        $this->not_condition = false;
    }

    function get_condition()
    {
        return $this->condition;
    }

    function get_measure_treshold()
    {
        return $this->measure_treshold;
    }

    function get_not_condition()
    {
        return $this->not_condition;
    }

    function get_referenced_objective()
    {
        return $this->referenced_objective;
    }

    function set_condition($condition)
    {
        $this->condition = $condition;
    }

    function set_measure_treshold($measure_treshold)
    {
        $this->measure_treshold = $measure_treshold;
    }

    function set_not_condition($not_condition)
    {
        $this->not_condition = $not_condition;
    }

    function set_referenced_objective($referenced_objective)
    {
        $this->referenced_objective = $referenced_objective;
    }

}
?>