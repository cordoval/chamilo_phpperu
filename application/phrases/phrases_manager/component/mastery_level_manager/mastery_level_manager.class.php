<?php
/**
 * @package admin.lib.package_manager
 * @author Hans De Bisschop
 */

class PhrasesMasteryLevelManager extends SubManager
{
    const PARAM_MASTERY_LEVEL_MANAGER_ACTION = 'action';
    const PARAM_PHRASES_MASTERY_LEVEL_ID = 'level';
    const PARAM_MOVE = 'move';
    
    const ACTION_BROWSE = 'browser';
    const ACTION_CREATE = 'creator';
    const ACTION_UPDATE = 'updater';
    const ACTION_DELETE = 'deleter';
    const ACTION_VIEW = 'viewer';
    const ACTION_MOVE = 'mover';
    const ACTION_MOVE_UP = 'up_mover';
    const ACTION_MOVE_DOWN = 'down_mover';

    function PhrasesMasteryLevelManager($phrases_manager)
    {
        parent :: __construct($phrases_manager);
        
        $publication_action = Request :: get(self :: PARAM_MASTERY_LEVEL_MANAGER_ACTION);
        if ($publication_action)
        {
            $this->set_action($publication_action);
        }
    }

    function set_action($action)
    {
        $this->set_parameter(self :: PARAM_MASTERY_LEVEL_MANAGER_ACTION, $action);
    }

    function get_action()
    {
        return $this->get_parameter(self :: PARAM_MASTERY_LEVEL_MANAGER_ACTION);
    }

    function get_application_component_path()
    {
        return dirname(__FILE__) . '/component/';
    }

    function retrieve_phrases_mastery_levels($condition = null, $offset = 0, $max_objects = -1, $order_by = array ())
    {
        return $this->get_parent()->retrieve_phrases_mastery_levels($condition, $order_by, $offset, $max_objects);
    }

    function retrieve_phrases_mastery_level($id)
    {
        return $this->get_parent()->retrieve_phrases_mastery_level($id);
    }

    function count_phrases_mastery_levels($condition = null)
    {
        return $this->get_parent()->count_phrases_mastery_levels($condition);
    }

    function get_delete_phrases_mastery_level_url($mastery_level)
    {
        return $this->get_parent()->get_url(array(self :: PARAM_MASTERY_LEVEL_MANAGER_ACTION => self :: ACTION_DELETE, self :: PARAM_PHRASES_MASTERY_LEVEL_ID => $mastery_level->get_id()));
    }

    function get_update_phrases_mastery_level_url($mastery_level)
    {
        return $this->get_parent()->get_url(array(self :: PARAM_MASTERY_LEVEL_MANAGER_ACTION => self :: ACTION_UPDATE, self :: PARAM_PHRASES_MASTERY_LEVEL_ID => $mastery_level->get_id()));
    }

    /**
     * Helper function for the SubManager class,
     * pending access to class constants via variables in PHP 5.3
     * e.g. $name = $class :: DEFAULT_ACTION
     *
     * DO NOT USE IN THIS SUBMANAGER'S CONTEXT
     * Instead use:
     * - self :: DEFAULT_ACTION in the context of this class
     * - YourSubManager :: DEFAULT_ACTION in all other application classes
     */
    static function get_default_action()
    {
        return self :: DEFAULT_ACTION;
    }

    /**
     * Helper function for the SubManager class,
     * pending access to class constants via variables in PHP 5.3
     * e.g. $name = $class :: PARAM_ACTION
     *
     * DO NOT USE IN THIS SUBMANAGER'S CONTEXT
     * Instead use:
     * - self :: PARAM_ACTION in the context of this class
     * - YourSubManager :: PARAM_ACTION in all other application classes
     */
    static function get_action_parameter()
    {
        return self :: PARAM_MASTERY_LEVEL_MANAGER_ACTION;
    }

    /**
     * @param Application $application
     */
    static function launch($application)
    {
        parent :: launch(__CLASS__, $application);
    }
}
?>