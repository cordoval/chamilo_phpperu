<?php
/**
 * $Id: rights_template_manager.class.php 214 2009-11-13 13:57:37Z vanpouckesven $
 * @package rights.lib.rights_template_manager
 */

class RightsTemplateManager extends SubManager
{
    const PARAM_RIGHTS_TEMPLATE_ID = 'template';
    const PARAM_RIGHTS_TEMPLATE_ACTION = 'action';
    const PARAM_SOURCE = 'source';
    const PARAM_LOCATION = 'location';
    
    const ACTION_BROWSE_RIGHTS_TEMPLATES = 'browse';
    const ACTION_EDIT_RIGHTS_TEMPLATE = 'edit';
    const ACTION_DELETE_RIGHTS_TEMPLATES = 'delete';
    const ACTION_CREATE_RIGHTS_TEMPLATE = 'create';
    const ACTION_CONFIGURE_RIGHTS_TEMPLATES = 'configure';
    const ACTION_CONFIGURE_LOCATION_RIGHTS_TEMPLATES = 'template';
    const ACTION_LOCK_RIGHTS_TEMPLATES = 'lock';
    const ACTION_UNLOCK_RIGHTS_TEMPLATES = 'unlock';
    const ACTION_INHERIT_RIGHTS_TEMPLATES = 'inherit';
    const ACTION_DISINHERIT_RIGHTS_TEMPLATES = 'disinherit';
    const ACTION_SET_RIGHTS_TEMPLATES = 'set';

    function RightsTemplateManager($rights_manager)
    {
        parent :: __construct($rights_manager);
        
        $rights_template_action = Request :: get(self :: PARAM_RIGHTS_TEMPLATE_ACTION);
        if ($rights_template_action)
        {
            $this->set_parameter(self :: PARAM_RIGHTS_TEMPLATE_ACTION, $rights_template_action);
        }
    }

    function run()
    {
        $rights_template_action = $this->get_parameter(self :: PARAM_RIGHTS_TEMPLATE_ACTION);
        
        switch ($rights_template_action)
        {
            case self :: ACTION_BROWSE_RIGHTS_TEMPLATES :
                $component = RightsTemplateManagerComponent :: factory('Browser', $this);
                break;
            case self :: ACTION_EDIT_RIGHTS_TEMPLATE :
                $component = RightsTemplateManagerComponent :: factory('Editor', $this);
                break;
            case self :: ACTION_DELETE_RIGHTS_TEMPLATES :
                $component = RightsTemplateManagerComponent :: factory('Deleter', $this);
                break;
            case self :: ACTION_CREATE_RIGHTS_TEMPLATE :
                $component = RightsTemplateManagerComponent :: factory('Creator', $this);
                break;
            case self :: ACTION_CONFIGURE_RIGHTS_TEMPLATES :
                $component = RightsTemplateManagerComponent :: factory('Configurer', $this);
                break;
            case self :: ACTION_CONFIGURE_LOCATION_RIGHTS_TEMPLATES :
                $component = RightsTemplateManagerComponent :: factory('RightsTemplater', $this);
                break;
            case self :: ACTION_LOCK_RIGHTS_TEMPLATES :
                $component = RightsTemplateManagerComponent :: factory('Locker', $this);
                break;
            case self :: ACTION_UNLOCK_RIGHTS_TEMPLATES :
                $component = RightsTemplateManagerComponent :: factory('Unlocker', $this);
                break;
            case self :: ACTION_INHERIT_RIGHTS_TEMPLATES :
                $component = RightsTemplateManagerComponent :: factory('Inheriter', $this);
                break;
            case self :: ACTION_DISINHERIT_RIGHTS_TEMPLATES :
                $component = RightsTemplateManagerComponent :: factory('Disinheriter', $this);
                break;
            case self :: ACTION_SET_RIGHTS_TEMPLATES :
                $component = RightsTemplateManagerComponent :: factory('Setter', $this);
                break;
            default :
                $component = RightsTemplateManagerComponent :: factory('Browser', $this);
                break;
        }
        
        $component->run();
    }

    function get_application_component_path()
    {
        return Path :: get_rights_path() . 'lib/rights_template_manager/component/';
    }

    function get_rights_template_deleting_url($rights_template)
    {
        return $this->get_url(array(self :: PARAM_RIGHTS_TEMPLATE_ACTION => self :: ACTION_DELETE_RIGHTS_TEMPLATES, self :: PARAM_RIGHTS_TEMPLATE_ID => $rights_template->get_id()));
    }

    function get_rights_template_editing_url($rights_template)
    {
        return $this->get_url(array(self :: PARAM_RIGHTS_TEMPLATE_ACTION => self :: ACTION_EDIT_RIGHTS_TEMPLATE, self :: PARAM_RIGHTS_TEMPLATE_ID => $rights_template->get_id()));
    }

    function get_manage_rights_template_rights_url($rights_template)
    {
        return $this->get_url(array(self :: PARAM_RIGHTS_TEMPLATE_ACTION => self :: ACTION_CONFIGURE_RIGHTS_TEMPLATES, self :: PARAM_RIGHTS_TEMPLATE_ID => $rights_template->get_id()));
    }

    function retrieve_rights_templates($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->get_parent()->retrieve_rights_templates($condition, $offset, $count, $order_property);
    }

    function count_rights_templates($conditions = null)
    {
        return $this->get_parent()->count_rights_templates($conditions);
    }

    function retrieve_locations($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->get_parent()->retrieve_locations($condition, $offset, $count, $order_property);
    }

    function count_locations($conditions = null)
    {
        return $this->get_parent()->count_locations($conditions);
    }

    function retrieve_location($location_id)
    {
        return $this->get_parent()->retrieve_location($location_id);
    }

    function retrieve_rights_template($id)
    {
        return $this->get_parent()->retrieve_rights_template($id);
    }

    function retrieve_rights_template_right_location($right_id, $rights_template_id, $location_id)
    {
        return $this->get_parent()->retrieve_rights_template_right_location($right_id, $rights_template_id, $location_id);
    }
}
?>