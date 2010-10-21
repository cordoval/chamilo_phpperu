<?php namespace survey;

require_once Path :: get_application_path() . 'lib/survey/reporting_manager/component/publication_rel_reporting_template_table/table.class.php';
require_once Path :: get_application_path() . 'lib/survey/reporting_manager/component/reporting_template_table/table.class.php';

class SurveyExportManager extends SubManager
{
    
    const PARAM_ACTION = 'action';
    
    const PARAM_CONTEXT_TEMPLATE_ID = 'context_template_id';
    const PARAM_REPORTING_TEMPLATE_REGISTRATION_ID = 'template_registration_id';
    
    const PARAM_PUBLICATION_REL_REPORTING_TEMPLATE_ID = 'publication_rel_reporting_template_id';
    
    //used for import
    const PARAM_MESSAGE = 'message';
    const PARAM_WARNING_MESSAGE = 'warning_message';
    const PARAM_ERROR_MESSAGE = 'error_message';
    
    const ACTION_CREATE = 'creator';
    const ACTION_BROWSE = 'browser';
    const ACTION_EDIT = 'editor';
    const ACTION_EDIT_REPORTING_RIGHTS = 'rights_editor';
    const ACTION_DELETE = 'deleter';
    const ACTION_REPORTING = 'reporting';
    
    const DEFAULT_ACTION = self :: ACTION_BROWSE;

    function SurveyExportManager($survey_manager)
    {
        parent :: __construct($survey_manager);
    }

    function get_application_component_path()
    {
        return Path :: get_application_path() . 'lib/survey/export_manager/component/';
    }

    //url
    

    function get_browse_reporting_templates_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE));
    }

    function get_publication_reporting_template_create_url($reporting_template_registation)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE, self :: PARAM_REPORTING_TEMPLATE_REGISTRATION_ID => $reporting_template_registation->get_id()));
    }

    function get_publication_reporting_template_delete_url($publication_rel_reporting_template_registation)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE, self :: PARAM_PUBLICATION_REL_REPORTING_TEMPLATE_ID => $publication_rel_reporting_template_registation->get_id()));
    }

    function get_publication_reporting_template_edit_url($publication_rel_reporting_template_registation)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EDIT, self :: PARAM_PUBLICATION_REL_REPORTING_TEMPLATE_ID => $publication_rel_reporting_template_registation->get_id()));
    }

    function get_reporting_rights_editor_url($publication_rel_reporting_template_registation)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EDIT_REPORTING_RIGHTS, self :: PARAM_PUBLICATION_REL_REPORTING_TEMPLATE_ID => $publication_rel_reporting_template_registation->get_id()));
    }

    function get_reporting_url($publication_rel_reporting_template_registation)
    {
        $context_template_id = $publication_rel_reporting_template_registation->get_level();
        if ($context_template_id != 0)
        {
            return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_REPORTING, self :: PARAM_PUBLICATION_REL_REPORTING_TEMPLATE_ID => $publication_rel_reporting_template_registation->get_id(), self :: PARAM_CONTEXT_TEMPLATE_ID => $publication_rel_reporting_template_registation->get_level()));
        }
        else
        {
            return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_REPORTING, self :: PARAM_PUBLICATION_REL_REPORTING_TEMPLATE_ID => $publication_rel_reporting_template_registation->get_id()));
        }
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