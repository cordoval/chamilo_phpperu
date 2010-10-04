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
    
    const ACTION_BROWSE_TYPE_TEMPLATES = 'browser';
    const ACTION_EDIT_TYPE_TEMPLATE = 'editor';
    const ACTION_DELETE_TYPE_TEMPLATES = 'deleter';
    const ACTION_CREATE_TYPE_TEMPLATE = 'creator';
    const ACTION_CONFIGURE_TYPE_TEMPLATES = 'rights_templater';
    
    const DEFAULT_ACTION = self :: ACTION_BROWSE_TYPE_TEMPLATES;

    function TypeTemplateManager($rights_manager)
    {
        parent :: __construct($rights_manager);
        
        $type_template_action = Request :: get(self :: PARAM_TYPE_TEMPLATE_ACTION);
        if ($type_template_action)
        {
            $this->set_parameter(self :: PARAM_TYPE_TEMPLATE_ACTION, $type_template_action);
        }
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

    function get_manage_type_template_rights_url($type_template)
    {
        return $this->get_url(array(self :: PARAM_TYPE_TEMPLATE_ACTION => self :: ACTION_CONFIGURE_TYPE_TEMPLATES, self :: PARAM_TYPE_TEMPLATE_ID => $type_template->get_id()));
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
        return self :: PARAM_TYPE_TEMPLATE_ACTION;
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