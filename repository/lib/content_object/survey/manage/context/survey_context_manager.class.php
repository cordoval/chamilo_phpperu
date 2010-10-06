<?php

require_once Path :: get_repository_path() . 'lib/content_object/survey/context_data_manager/context_data_manager.class.php';

/**
 * @package repository.lib.content_object.survey.manage.context
 *
 * @author Eduard Vossen
 * @author Hans De Bisschop
 */
class SurveyContextManager extends SubManager
{
    
    const PARAM_ACTION = 'action';
    
    const PARAM_CONTEXT_REGISTRATION_ID = 'context_registration_id';
    const PARAM_CONTEXT_TEMPLATE_ID = 'context_template_id';
    const PARAM_CONTEXT_ID = 'context_id';
    const PARAM_TEMPLATE_ID = 'template_id';
    const PARAM_CONTEXT = 'context';
    
    const ACTION_MANAGER_CHOOSER = 'manager_chooser';
    
    const ACTION_CREATE_CONTEXT_REGISTRATION = 'registration_creator';
    const ACTION_EDIT_CONTEXT_REGISTRATION = 'registration_updater';
    const ACTION_DELETE_CONTEXT_REGISTRATION = 'registration_deleter';
    const ACTION_VIEW_CONTEXT_REGISTRATION = 'registration_viewer';
    const ACTION_BROWSE_CONTEXT_REGISTRATION = 'registration_browser';
    
    const ACTION_CREATE_CONTEXT_TEMPLATE = 'context_template_creator';
    const ACTION_EDIT_CONTEXT_TEMPLATE = 'context_template_updater';
    const ACTION_DELETE_CONTEXT_TEMPLATE = 'context_template_deleter';
    const ACTION_VIEW_CONTEXT_TEMPLATE = 'context_template_viewer';
    const ACTION_BROWSE_CONTEXT_TEMPLATE = 'context_template_browser';
    
    const ACTION_CREATE_CONTEXT = 'context_creator';
    const ACTION_EDIT_CONTEXT = 'context_updater';
    const ACTION_DELETE_CONTEXT = 'context_deleter';
    
    const ACTION_CREATE_TEMPLATE = 'template_creator';
    const ACTION_EDIT_TEMPLATE = 'template_updater';
    const ACTION_DELETE_TEMPLATE = 'template_deleter';
    
    const DEFAULT_ACTION = self :: ACTION_MANAGER_CHOOSER;

    function SurveyContextManager($repository_manager)
    {
        parent :: __construct($repository_manager);
    }

    function get_application_component_path()
    {
        return Path :: get_repository_path() . 'lib/content_object/survey/manage/context/component/';
    }

    function get_context_registration_browsing_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_CONTEXT_REGISTRATION));
    }

    function get_context_registration_viewing_url($context_registration)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_VIEW_CONTEXT_REGISTRATION, self :: PARAM_CONTEXT_REGISTRATION_ID => $context_registration->get_id()));
    }

    function get_context_registration_creation_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE_CONTEXT_REGISTRATION));
    }

    function get_context_registration_update_url($context_registration)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EDIT_CONTEXT_REGISTRATION, self :: PARAM_CONTEXT_REGISTRATION_ID => $context_registration->get_id()));
    }

    function get_context_registration_delete_url($context_registration)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE_CONTEXT_REGISTRATION, self :: PARAM_CONTEXT_REGISTRATION_ID => $context_registration->get_id()));
    }

    function get_context_creation_url($context_registration)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE_CONTEXT, self :: PARAM_CONTEXT_REGISTRATION_ID => $context_registration->get_id()));
    }

    function get_context_update_url($context_registration_id, $survey_context)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EDIT_CONTEXT, self :: PARAM_CONTEXT_REGISTRATION_ID => $context_registration_id, self :: PARAM_CONTEXT_ID => $survey_context->get_id()));
    }

    function get_context_template_browsing_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_CONTEXT_TEMPLATE));
    }

    function get_context_template_viewing_url($context_template)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_VIEW_CONTEXT_TEMPLATE, self :: PARAM_CONTEXT_TEMPLATE_ID => $context_template->get_id()));
    }

    function get_context_template_creation_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE_CONTEXT_TEMPLATE));
    }

    function get_context_template_delete_url($context_template)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE_CONTEXT_TEMPLATE, self :: PARAM_CONTEXT_TEMPLATE_ID => $context_template->get_id()));
    }

    function get_context_template_update_url($context_template)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EDIT_CONTEXT_TEMPLATE, self :: PARAM_CONTEXT_TEMPLATE_ID => $context_template->get_id()));
    }

    function get_template_creation_url($context_template)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE_TEMPLATE, self :: PARAM_CONTEXT_TEMPLATE_ID => $context_template->get_id()));
    }

    function get_template_update_url($template)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EDIT_TEMPLATE, self :: PARAM_TEMPLATE_ID => $template->get_id()));
    }

    static function launch($application)
    {
        parent :: launch(__CLASS__, $application);
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
        return self :: PARAM_ACTION;
    }
    
    function display_header()
    {
    	Application :: display_header();
    }
    
    function display_footer()
    {
    	Application :: display_footer();
    }
    
    function has_menu()
    {
    	return false;
    }
}

?>