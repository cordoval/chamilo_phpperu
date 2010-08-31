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
    
    const ACTION_BROWSE_RIGHTS_TEMPLATES = 'browser';
    const ACTION_EDIT_RIGHTS_TEMPLATE = 'editor';
    const ACTION_DELETE_RIGHTS_TEMPLATES = 'deleter';
    const ACTION_CREATE_RIGHTS_TEMPLATE = 'creator';
    const ACTION_CONFIGURE_RIGHTS_TEMPLATES = 'configurer';
    const ACTION_CONFIGURE_LOCATION_RIGHTS_TEMPLATES = 'rights_templater';
    const ACTION_LOCK_RIGHTS_TEMPLATES = 'locker';
    const ACTION_UNLOCK_RIGHTS_TEMPLATES = 'unlocker';
    const ACTION_INHERIT_RIGHTS_TEMPLATES = 'inheriter';
    const ACTION_DISINHERIT_RIGHTS_TEMPLATES = 'disinheriter';
    const ACTION_SET_RIGHTS_TEMPLATES = 'setter';
    
    const DEFAULT_ACTION = self :: ACTION_BROWSE_RIGHTS_TEMPLATES;

    function RightsTemplateManager($rights_manager)
    {
        parent :: __construct($rights_manager);
        
        $rights_template_action = Request :: get(self :: PARAM_RIGHTS_TEMPLATE_ACTION);
        if ($rights_template_action)
        {
            $this->set_parameter(self :: PARAM_RIGHTS_TEMPLATE_ACTION, $rights_template_action);
        }
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
        return self :: PARAM_RIGHTS_TEMPLATE_ACTION;
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