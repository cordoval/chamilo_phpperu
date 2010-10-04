<?php
/**
 * $Id: external_repository_instance_manager.class.php 126 2009-11-09 13:11:05Z vanpouckesven $
 * @package repository.lib.external_repository_instance_manager
 * @author Hans De Bisschop
 */
//require_once dirname(__FILE__) . '/component/registration_browser/registration_browser_table.class.php';


class ExternalRepositoryInstanceManager extends SubManager
{
    const PARAM_INSTANCE_ACTION = 'action';
    const PARAM_INSTANCE = 'instance';
    const PARAM_EXTERNAL_REPOSITORY_TYPE = 'type';

    const ACTION_BROWSE_INSTANCES = 'browser';
    const ACTION_ACTIVATE_INSTANCE = 'activator';
    const ACTION_DEACTIVATE_INSTANCE = 'deactivator';
    const ACTION_UPDATE_INSTANCE = 'updater';
    const ACTION_DELETE_INSTANCE = 'remover';
    const ACTION_CREATE_INSTANCE = 'creator';
    const ACTION_MANAGE_INSTANCE_RIGHTS = 'rights_editor';

    const DEFAULT_ACTION = self :: ACTION_BROWSE_INSTANCES;

    function ExternalRepositoryInstanceManager($repository_manager)
    {
        parent :: __construct($repository_manager);

        $instance_action = Request :: get(self :: PARAM_INSTANCE_ACTION);
        if ($instance_action)
        {
            $this->set_action($instance_action);
        }
    }

    function set_action($action)
    {
        $this->set_parameter(self :: PARAM_INSTANCE_ACTION, $action);
    }

    function get_action()
    {
        return $this->get_parameter(self :: PARAM_INSTANCE_ACTION);
    }

    function get_application_component_path()
    {
        return Path :: get_repository_path() . 'lib/external_repository_instance_manager/component/';
    }

    function count_external_repositories($condition = null)
    {
        return $this->get_parent()->count_external_repositories($condition);
    }

    function retrieve_external_repositories($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->get_parent()->retrieve_external_repositories($condition, $offset, $count, $order_property);
    }

    static function launch($application)
    {
        parent :: launch(__CLASS__, $application);
    }

    function retrieve_external_repository($external_repository_id)
    {
        return $this->get_parent()->retrieve_external_repository($external_repository_id);
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
        return self :: PARAM_INSTANCE_ACTION;
    }
}
?>