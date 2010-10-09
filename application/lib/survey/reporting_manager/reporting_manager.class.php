<?php

//require_once Path :: get_application_path() . 'lib/survey/reporting_manager/component/browser/browser_table.class.php';


class SurveyReportingManager extends SubManager
{
    
    const PARAM_ACTION = 'action';
    
    const PARAM_REPORTING_TEMPLATE_ID = 'template_id';
    
    //used for import
    const PARAM_MESSAGE = 'message';
    const PARAM_WARNING_MESSAGE = 'warning_message';
    const PARAM_ERROR_MESSAGE = 'error_message';
    
    const ACTION_ACTIVATE_REPORTING_TEMPLATE = 'activator';
    const ACTION_BROWSE_REPORTING_TEMPLATES = 'browser';
    const ACTION_EDIT_REPORTING_TEMPLATE = 'editor';
    const ACTION_DEACTIVATE_REPORTING_TEMPLATE = 'deleter';
    const ACTION_IMPORT_REPORTING_TEMPLATE = 'importer';
    
    const DEFAULT_ACTION = self :: ACTION_BROWSE_REPORTING_TEMPLATES;

    function InternshipOrganizerRegionManager($survey_manager)
    {
        parent :: __construct($survey_manager);
    }

    function get_application_component_path()
    {
        return Path :: get_application_path() . 'lib/survey/reporting_manager/component/';
    }

    //url
    

    function get_browse_reporting_templates_url($reporting_template)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_REPORTING_TEMPLATES));
    }

    function get_reporting_template_editing_url($reporting_template)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EDIT_REPORTING_TEMPLATE, self :: PARAM_REPORTING_TEMPLATE_ID => $reporting_template->get_id()));
    }

    function get_reporting_template_activate_url()
    {
            return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_ACTIVATE_REPORTING_TEMPLATE));
      
    }

    function get_reporting_template_deactivate_url($reporting_template)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DEACTIVATE_REPORTING_TEMPLATE, self :: PARAM_REPORTING_TEMPLATE_ID => $reporting_template->get_id()));
    }

    function get_reporting_template_importer_url($reporting_template_id)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_IMPORT_REPORTING_TEMPLATE, self :: PARAM_REPORTING_TEMPLATE_ID => $reporting_template_id));
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

    /**
     * @param Application $application
     */
    static function launch($application)
    {
        parent :: launch(__CLASS__, $application);
    }
}

?>