<?php
/**
 * $Id: scorm_item.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.scorm_item
 */
require_once dirname(__FILE__) . '/objectives/objectives.class.php';
require_once dirname(__FILE__) . '/condition_rules/condition_rules.class.php';

class ScormItem extends ContentObject
{
    const PROPERTY_PATH = 'path';
    const PROPERTY_VISIBLE = 'visible';
    const PROPERTY_PARAMETERS = 'parameters';
    const PROPERTY_TIME_LIMIT_ACTION = 'time_limit_action';
    const PROPERTY_DATA_FROM_LMS = 'data_from_lms';
    const PROPERTY_COMPLETION_TRESHOLD = 'completion_treshold';
    const PROPERTY_HIDE_LMS_UI = 'hide_lms_ui';
    const PROPERTY_CONTROL_MODE = 'control_mode';
    const PROPERTY_TIME_LIMIT = 'time_limit';
    const PROPERTY_OBJECTIVES = 'objectives';
    const PROPERTY_CONDITION_RULES = 'condition_rules';
    const PROPERTY_COMPLETION_SET_BY_CONTENT = 'completion_set_by_content';
    const PROPERTY_OBJECTIVE_SET_BY_CONTENT = 'objective_set_by_content';
    const PROPERTY_IDENTIFIER = 'identifier';

    static function get_additional_property_names()
    {
        return array(self :: PROPERTY_PATH, self :: PROPERTY_VISIBLE, self :: PROPERTY_PARAMETERS, self :: PROPERTY_TIME_LIMIT_ACTION, self :: PROPERTY_DATA_FROM_LMS, self :: PROPERTY_COMPLETION_TRESHOLD, self :: PROPERTY_HIDE_LMS_UI, self :: PROPERTY_CONTROL_MODE, self :: PROPERTY_TIME_LIMIT, self :: PROPERTY_OBJECTIVES, self :: PROPERTY_CONDITION_RULES, self :: PROPERTY_COMPLETION_SET_BY_CONTENT, self :: PROPERTY_OBJECTIVE_SET_BY_CONTENT, self :: PROPERTY_IDENTIFIER);
    }
    
    private $prerequisites;
    private $mastery_score;

    function get_prerequisites()
    {
        return $this->prerequisites;
    }

    function set_prerequisites($prerequisites)
    {
        $this->prerequisites = $prerequisites;
    }

    function get_mastery_score()
    {
        return $this->mastery_score;
    }

    function set_mastery_score($mastery_score)
    {
        $this->mastery_score = $mastery_score;
    }

    function get_path()
    {
        return $this->get_additional_property(self :: PROPERTY_PATH);
    }

    function set_path($path)
    {
        $this->set_additional_property(self :: PROPERTY_PATH, $path);
    }

    function get_visible()
    {
        return $this->get_additional_property(self :: PROPERTY_VISIBLE);
    }

    function set_visible($visible)
    {
        $this->set_additional_property(self :: PROPERTY_VISIBLE, $visible);
    }

    function get_parameters()
    {
        return $this->get_additional_property(self :: PROPERTY_PARAMETERS);
    }

    function set_parameters($parameters)
    {
        $this->set_additional_property(self :: PROPERTY_PARAMETERS, $parameters);
    }

    function get_time_limit_action()
    {
        return $this->get_additional_property(self :: PROPERTY_TIME_LIMIT_ACTION);
    }

    function set_time_limit_action($time_limit_action)
    {
        $this->set_additional_property(self :: PROPERTY_TIME_LIMIT_ACTION, $time_limit_action);
    }

    function get_data_from_lms()
    {
        return $this->get_additional_property(self :: PROPERTY_DATA_FROM_LMS);
    }

    function set_data_from_lms($data_from_lms)
    {
        $this->set_additional_property(self :: PROPERTY_DATA_FROM_LMS, $data_from_lms);
    }

    function get_completion_treshold()
    {
        return $this->get_additional_property(self :: PROPERTY_COMPLETION_TRESHOLD);
    }

    function set_completion_treshold($completion_treshold)
    {
        $this->set_additional_property(self :: PROPERTY_COMPLETION_TRESHOLD, $completion_treshold);
    }

    function get_hide_lms_ui()
    {
        return unserialize($this->get_additional_property(self :: PROPERTY_HIDE_LMS_UI));
    }

    function set_hide_lms_ui($hide_lms_ui)
    {
        $this->set_additional_property(self :: PROPERTY_HIDE_LMS_UI, serialize($hide_lms_ui));
    }

    function get_control_mode()
    {
        return unserialize($this->get_additional_property(self :: PROPERTY_CONTROL_MODE));
    }

    function get_time_limit()
    {
        return $this->get_additional_property(self :: PROPERTY_TIME_LIMIT);
    }

    function set_time_limit($time_limit)
    {
        $this->set_additional_property(self :: PROPERTY_TIME_LIMIT, $time_limit);
    }

    function set_control_mode($control_mode)
    {
        if (! is_array($control_mode))
            $control_mode = array($control_mode);
        
        $this->set_additional_property(self :: PROPERTY_CONTROL_MODE, serialize($control_mode));
    }

    function set_objectives($objectives)
    {
        $this->set_additional_property(self :: PROPERTY_OBJECTIVES, serialize($objectives));
    }

    function get_objectives()
    {
        return unserialize($this->get_additional_property(self :: PROPERTY_OBJECTIVES));
    }

    function add_objective($objective, $primary = false)
    {
        $objectives = $this->get_objectives();
        if (! $objectives)
            $objectives = new Objectives();
        
        $objectives->add_objective($objective, $primary);
        $this->set_objectives($objectives);
    }

    function set_condition_rules($condition_rules)
    {
        $this->set_additional_property(self :: PROPERTY_CONDITION_RULES, serialize($condition_rules));
    }

    function get_condition_rules()
    {
        return unserialize($this->get_additional_property(self :: PROPERTY_CONDITION_RULES));
    }

    function add_condition_rule($condition_rule, $type = 'pre')
    {
        $condition_rules = $this->get_condition_rules();
        if (! $condition_rules)
            $condition_rules = new ConditionRules();
        
        $condition_rules->add_condition_rule($condition_rule, $type);
        $this->set_condition_rules($condition_rules);
    }

    function set_completion_set_by_content($completion_set_by_content)
    {
        $this->set_additional_property(self :: PROPERTY_COMPLETION_SET_BY_CONTENT, $completion_set_by_content);
    }

    function get_completion_set_by_content()
    {
        return $this->get_additional_property(self :: PROPERTY_COMPLETION_SET_BY_CONTENT);
    }

    function set_objective_set_by_content($objective_set_by_content)
    {
        $this->set_additional_property(self :: PROPERTY_OBJECTIVE_SET_BY_CONTENT, $objective_set_by_content);
    }

    function get_objective_set_by_content()
    {
        return $this->get_additional_property(self :: PROPERTY_OBJECTIVE_SET_BY_CONTENT);
    }

    function get_identifier()
    {
        return $this->get_additional_property(self :: PROPERTY_IDENTIFIER);
    }

    function set_identifier($identifier)
    {
        $this->set_additional_property(self :: PROPERTY_IDENTIFIER, $identifier);
    }

    function get_url($include_parameters = false)
    {
        $url = Path :: get(WEB_SCORM_PATH) . $this->get_path();
        
        if ($include_parameters)
            $url = $this->add_parameters_to_url($url);
        
        return $url;
    }

    function get_full_path()
    {
        return Path :: get(SYS_SCORM_PATH) . $this->get_path();
    }

    function add_parameters_to_url($url)
    {
        $parameters = $this->get_parameters();
        
        while ((substr($parameters, 0, 1) == '&') || (substr($parameters, 0, 1) == '?'))
        {
            $parameters = substr($parameters, 1, strlen($parameters) - 1);
        }
        
        if (substr($parameters, 0, 1) == '#')
        {
            if (substr($url, 0, 1) == '#')
            {
                return $url;
            }
            else
            {
                return $url . $parameters;
            }
        }
        
        if (substr_count($url, '?') > 0)
        {
            return $url . '&' . $parameters;
        }
        else
        {
            return $url . '?' . $parameters;
        }
    }
}
?>