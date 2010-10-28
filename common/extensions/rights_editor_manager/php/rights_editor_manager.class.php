<?php
namespace common\extensions\rights_editor_manager;
use common\libraries\SubManager;
use common\libraries\Session;
use common\libraries\Request;
use common\libraries\Path
;use common\libraries\Utilities;

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
    const ACTION_SET_TEMPLATE_RIGHTS = 'set_template_rights';
    const ACTION_CHANGE_INHERIT = 'change_inherit';

    const PARAM_GROUP = 'group_id';

    const TYPE_USER = 'user';
    const TYPE_GROUP = 'group';
    const TYPE_TEMPLATE = 'template';

    private $locations;
    private $excluded_groups;
    private $excluded_users;
    private $limited_groups;
    private $limited_users;
    private $limited_templates;
    
    private $types;

    /**
     * @param unknown_type $parent
     * @param array $locations
     */
    function RightsEditorManager($parent, $locations)
    {
        parent :: __construct($parent);

        $this->locations = $locations;
        $this->excluded_users = array(Session :: get_user_id());
        $this->excluded_groups = array();

        $this->included_users = array();
        $this->included_groups = array();

        $this->types = array(self :: TYPE_USER, self :: TYPE_GROUP, self :: TYPE_TEMPLATE);
        
        $rights_editor_action = Request :: get(self :: PARAM_RIGHTS_EDITOR_ACTION);
        if ($rights_editor_action)
        {
            $this->set_parameter(self :: PARAM_RIGHTS_EDITOR_ACTION, $rights_editor_action);
        }
    }

    function factory($content_object, $parent, $locations)
    {
    	if ($content_object)
        {
            $type = $content_object->get_type_name();
            $file = Path :: get_repository_path() . 'content_object/' . $type . '/php/rights/' . $type . '_rights_editor_manager.class.php';
            if (file_exists($file))
            {
                require_once $file;
                $class = __NAMESPACE__ . '\\' . Utilities :: underscores_to_camelcase($type) . 'RightsEditorManager';
                $manager = new $class($parent, $locations);

                return $manager;
            }
        }
        return new RightsEditorManager($parent, $locations);
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
            case self :: ACTION_CHANGE_INHERIT:
                $component = $this->create_component('InheritChanger');
                break;
            default :
                $component = $this->create_component('Browser');
                break;
        }
        $component->run();
    }

    function get_application_component_path()
    {
        return Path :: get_common_extensions_path() . 'rights_editor_manager/php/component/';
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
            
    function exclude_templates($templates)
    {
        $this->excluded_templates = $templates;
    }

    function get_excluded_templates()
    {
        return $this->excluded_templates;
    }

    function get_excluded_users()
    {
        return $this->excluded_users;
    }

    function get_excluded_groups()
    {
        return $this->excluded_groups;
    }

    /**
     * @param Array $users An array of user ids
     */
    function limit_users(array $users)
    {
        $this->limited_users = $users;
    }
    
    /**
     * @param Array $templates An array of template ids
     */
    function limit_templates(array $templates)
    {
        $this->limited_templates = $templates;
    }

    /**
     * @param Array $groups An array of group ids
     */
    function limit_groups(array $groups)
    {
        $this->limited_groups = $groups;
    }

    function get_limited_users()
    {
        return $this->limited_users;
    }
    	
    function get_limited_templates()
    {
        return $this->limited_templates;
    }

    function get_limited_groups()
    {
        if (is_null($this->limited_groups))
        {

            return array();
        }
        else
        {
            return $this->limited_groups;
        }
    }

    function set_types(array $types)
    {
        $this->types = $types;
    }

    function get_types()
    {
        return $this->types;
    }

    function create_component($type, $application)
    {
        $component = parent :: create_component($type, $application);

        if (is_subclass_of($component, __CLASS__))
        {
            $component->set_locations($this->locations);
            $component->exclude_users($this->get_excluded_users());
            $component->exclude_groups($this->get_excluded_groups());
            $component->limit_users($this->get_limited_users());
            $component->limit_groups($this->get_limited_groups());
            $component->set_types($this->get_types());
        }

        return $component;
    }

}

?>