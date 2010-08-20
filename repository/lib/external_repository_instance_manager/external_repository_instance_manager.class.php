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
    
    const ACTION_BROWSE_INSTANCES = 'browse';
    const ACTION_ACTIVATE_INSTANCE = 'activate';
    const ACTION_DEACTIVATE_INSTANCE = 'deactivate';
    const ACTION_UPDATE_INSTANCE = 'update';
    const ACTION_DELETE_INSTANCE = 'remove';
    const ACTION_CREATE_INSTANCE = 'create';
    const ACTION_MANAGE_INSTANCE_RIGHTS = 'manage_rights';

    function ExternalRepositoryInstanceManager($repository_manager)
    {
        parent :: __construct($repository_manager);
        
        $instance_action = Request :: get(self :: PARAM_INSTANCE_ACTION);
        if ($instance_action)
        {
            $this->set_action($instance_action);
        }
    }

    function run()
    {
        
        $package_action = $this->get_action();
        
        switch ($package_action)
        {
            case self :: ACTION_BROWSE_INSTANCES :
                $component = $this->create_component('Browser');
                break;
            case self :: ACTION_CREATE_INSTANCE :
                $component = $this->create_component('Creator');
                break;
            case self :: ACTION_UPDATE_INSTANCE :
                $component = $this->create_component('Updater');
                break;
            case self :: ACTION_DELETE_INSTANCE :
                $component = $this->create_component('Deleter');
                break;
            case self :: ACTION_ACTIVATE_INSTANCE :
                $component = $this->create_component('Activator');
                break;
            case self :: ACTION_DEACTIVATE_INSTANCE :
                $component = $this->create_component('Deactivator');
                break;
            case self :: ACTION_MANAGE_INSTANCE_RIGHTS :
                $component = $this->create_component('RightsEditor');
                break;
            default :
                $component = $this->create_component('Browser');
                break;
        }
        
        $component->run();
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
    
    function retrieve_external_repository($external_repository_id)
    {
        return $this->get_parent()->retrieve_external_repository($external_repository_id);
    }
}
?>