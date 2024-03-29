<?php
namespace application\weblcms\tool\learning_path;

use application\weblcms\WeblcmsLpiAttemptObjectiveTracker;

use common\libraries\InCondition;
use common\libraries\AndCondition;
use common\libraries\EqualityCondition;

/**
 * $Id: rule_condition_translator.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.learning_path.component.learning_path_viewer
 */
class RuleConditionTranslator
{
    private $stop_forward_traversing;

    function __construct()
    {
        $this->stop_forward_traversing = false;
    }

    function get_status_from_item($object, $tracker_data)
    {
        if ($this->stop_forward_traversing)
            return 'disabled';

        if (($rules = $object->get_condition_rules()) == null)
            return 'enabled';

        if (($objectives = $object->get_objectives()) != null)
        {
            if (($primary_objective = $objectives->get_primary_objective()) == null)
            {
                $objective_trackers = null;
            }
            else
            {
                $ids = array();
                foreach ($tracker_data['trackers'] as $tracker)
                    $ids[] = $tracker->get_id();

                if (count($ids) == 0)
                {
                    $objective_trackers = null;
                }
                else
                {
                    $conditions[] = new InCondition(WeblcmsLpiAttemptObjectiveTracker :: PROPERTY_LPI_VIEW_ID, $ids);
                    $conditions[] = new EqualityCondition(WeblcmsLpiAttemptObjectiveTracker :: PROPERTY_OBJECTIVE_ID, $primary_objective->get_id());
                    $condition = new AndCondition($conditions);
                    $dummy = new WeblcmsLpiAttemptObjectiveTracker();
                    $objective_trackers = $dummy->retrieve_tracker_items($condition);

                }
            }
        }
        else
        {
            $objective_trackers = null;
        }

        $pre_condition_rules = $rules->get_precondition_rules();
        foreach ($pre_condition_rules as $pre_condition_rule)
        {
            $rules = $pre_condition_rule->get_conditions();
            $action = $pre_condition_rule->get_action();
            $operator = $pre_condition_rule->get_conditions_operator();
            switch ($operator)
            {
                case "all" :
                    $status = true;
                case "any" :
                    $status = false;
            }

            foreach ($rules as $rule)
            {
                switch ($rule->get_condition())
                {
                    case "satisfied" :
                        $condition_status = $this->check_for_satisfied($objective_trackers, $tracker_data);
                        break;
                    case "attempted" :
                        $condition_status = $this->check_for_attempted($tracker_data);
                        break;
                    case "completed" :
                        $condition_status = $this->check_for_completed($tracker_data);
                        break;
                }

                if ($rule->get_not_condition())
                    $condition_status = ! $condition_status;

                switch ($operator)
                {
                    case "all" :
                        $status &= $condition_status;
                        break;
                    case "any" :
                        $status |= $condition_status;
                        break;
                }
            }

            if ($status)
            {
                if ($action == 'stopFowardTraversal')
                {
                    $this->stop_forward_traversing = true;
                    return 'enabled';
                }

                return $action;
            }

        }

        return 'enabled';

    }

    private function check_for_satisfied($objective_trackers, $tracker_data)
    {
        if (is_array($objective_trackers))
        {
            foreach ($objective_trackers as $objective_tracker)
            {
                if ($objective_tracker->get_status() == 'completed')
                {
                    return true;
                }
            }
        }
        else
        {
            foreach ($tracker_data['trackers'] as $tracker)
            {
                if ($tracker->get_status() == 'completed')
                {
                    return true;
                }
            }
        }

        return false;
    }

    private function check_for_attempted($tracker_data)
    {
        return count($tracker_data['trackers']) > 0;
    }

    private function check_for_completed($tracker_data)
    {
        return ($tracker_data['completed'] == 1);
    }
}

?>