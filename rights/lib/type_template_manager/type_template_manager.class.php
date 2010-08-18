<?php
/**
 * $Id: type_template_manager.class.php 214 2009-11-13 13:57:37Z vanpouckesven $
 * @package rights.lib.rights_template_manager
 */

class TypeTemplateManager extends SubManager
{
    const PARAM_TYPE_TEMPLATE_ID = 'template';
    const PARAM_TYPE_TEMPLATE_ACTION = 'action';
    const PARAM_SOURCE = 'source';
    const PARAM_LOCATION = 'location';
    
    const ACTION_BROWSE_TYPE_TEMPLATES = 'browse';
    const ACTION_EDIT_TYPE_TEMPLATE = 'edit';
    const ACTION_DELETE_TYPE_TEMPLATES = 'delete';
    const ACTION_CREATE_TYPE_TEMPLATE = 'create';

    function TypeTemplateManager($rights_manager)
    {
        parent :: __construct($rights_manager);
        
        $type_template_action = Request :: get(self :: PARAM_TYPE_TEMPLATE_ACTION);
        if ($type_template_action)
        {
            $this->set_parameter(self :: PARAM_TYPE_TEMPLATE_ACTION, $type_template_action);
        }
    }

    function run()
    {
        $rights_template_action = $this->get_parameter(self :: PARAM_TYPE_TEMPLATE_ACTION);
        
        switch ($rights_template_action)
        {
            case self :: ACTION_BROWSE_TYPE_TEMPLATES :
                $component = $this->create_component('Browser');
                break;
            case self :: ACTION_EDIT_TYPE_TEMPLATE :
                $component = $this->create_component('Editor');
                break;
            case self :: ACTION_DELETE_TYPE_TEMPLATES :
                $component = $this->create_component('Deleter');
                break;
            case self :: ACTION_CREATE_TYPE_TEMPLATE :
                $component = $this->create_component('Creator');
                break;
            default :
                $component = $this->create_component('Browser');
                break;
        }
        
        $component->run();
    }

    function get_application_component_path()
    {
        return Path :: get_rights_path() . 'lib/type_template_manager/component/';
    }

    function get_type_template_deleting_url($type_template)
    {
        return $this->get_url(array(self :: PARAM_TYPE_TEMPLATE_ACTION => self :: ACTION_DELETE_TYPE_TEMPLATES, self :: PARAM_TYPE_TEMPLATE_ID => $type_template->get_id()));
    }

    function get_type_template_editing_url($type_template)
    {
        return $this->get_url(array(self :: PARAM_TYPE_TEMPLATE_ACTION => self :: ACTION_EDIT_TYPE_TEMPLATE, self :: PARAM_TYPE_TEMPLATE_ID => $type_template->get_id()));
    }

    function retrieve_type_templates($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->get_parent()->retrieve_type_templates($condition, $offset, $count, $order_property);
    }

    function count_type_templates($conditions = null)
    {
        return $this->get_parent()->count_type_templates($conditions);
    }

    function retrieve_type_template($id)
    {
        return $this->get_parent()->retrieve_type_template($id);
    }
}
?>