<?php
/**
 * $Id: rights_editor_manager.class.php 191 2009-11-13 11:50:28Z chellee $
 * @package application.common.rights_editor_manager
 */

class RightsEditorManager extends SubManager
{
    const PARAM_RIGHTS_EDITOR_ACTION = 'action';
    
    const ACTION_BROWSE_RIGHTS = 'browse';
    const ACTION_SET_USER_RIGHTS = 'set_user_rights';
    const ACTION_SET_GROUP_RIGHTS = 'set_group_rights';
    
    const PARAM_GROUP = 'group';
    
    private $locations;
    private $excluded_groups;
    private $excluded_users;

    function RightsEditorManager($parent, $locations)
    {
        parent :: __construct($parent);
        
        $this->locations = $locations;
        $this->exclude_users = array(Session :: get_user_id());
        $this->exclude_groups = array();

        $rights_editor_action = Request :: get(self :: PARAM_RIGHTS_EDITOR_ACTION);
        if ($rights_editor_action)
        {
            $this->set_parameter(self :: PARAM_RIGHTS_EDITOR_ACTION, $rights_editor_action);
        }
    }

    function run()
    {
        $parent = $this->get_parameter(self :: PARAM_RIGHTS_EDITOR_ACTION);
        
        switch ($parent)
        {
            case self :: ACTION_BROWSE_RIGHTS :
                $component = $this->create_component('Browser');
                break;
            case self :: ACTION_SET_USER_RIGHTS :
                $component = $this->create_component('UserRightsSetter');
                break;
            case self :: ACTION_SET_GROUP_RIGHTS :
                $component = $this->create_component('GroupRightsSetter');
                break;
            default :
                $component = $this->create_component('Browser');
                break;
        }
        
        $component->run();
    }

    function get_application_component_path()
    {
        return Path :: get_application_library_path() . 'rights_editor_manager/component/';
    }

    function get_locations()
    {
        return $this->locations;
    }
    
    function set_locations($locations)
    {
    	$this->locations = $locations;
    }

    function get_available_rights()
    {
        return $this->get_parent()->get_available_rights();
    }
    
    function exclude_users($users)
    {
    	$this->excluded_users = $users;
    }
    
 	function exclude_groups($groups)
    {
    	$this->excluded_groups = $groups;
    }
    
	function get_excluded_users()
    {
    	return $this->excluded_users;
    }
    
	function get_excluded_groups()
    {
    	return $this->excluded_groups;
    }

	function create_component($type, $application)
	{
		$component = parent :: create_component($type, $application);
		
		if(is_subclass_of($component, __CLASS__))
		{
			$component->set_locations($this->locations);
			$component->exclude_users($this->get_excluded_users());
			$component->exclude_groups($this->get_excluded_groups());
		}
		
		return $component;
	}
}
?>