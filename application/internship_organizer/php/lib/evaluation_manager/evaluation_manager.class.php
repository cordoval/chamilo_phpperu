<?php

require_once WebApplication :: get_application_class_lib_path('internship_organizer') . 'region_manager/component/browser/browser_table.class.php';
require_once WebApplication :: get_application_class_lib_path('internship_organizer') . 'region.class.php';

class InternshipOrganizerEvaluationManager extends SubManager
{
    
    const PARAM_ACTION = 'action';
    
    const PARAM_PUBLICATION_ID = 'publication_id';
    const PARAM_SURVEY_ID = 'survey_id';
    const PARAM_INVITEE_ID = 'invitee_id';        
    
	const ACTION_TAKE_EVALUATION = 'taker';
    
    
    const ACTION_CREATE_EVALUATION = 'creator';
    const ACTION_BROWSE_EVALUATIONS = 'browser';
    const ACTION_EDIT_EVALUATION = 'editor';
    const ACTION_DELETE_EVALUATION = 'deleter';
    
    const DEFAULT_ACTION = self :: ACTION_BROWSE_EVALUATIONS;

    function InternshipOrganizerRegionManager($internship_manager)
    {
        parent :: __construct($internship_manager);
    }

    function get_application_component_path()
    {
        return WebApplication :: get_application_class_lib_path('internship_organizer') . 'evaluation_manager/component/';
    }
 
    //url
    

    function get_take_evaluation_url($publication)
    {
        return $this->get_url(array( self :: PARAM_ACTION => self :: ACTION_TAKE_EVALUATION, self :: PARAM_PUBLICATION_ID => $publication->get_id(), self :: PARAM_SURVEY_ID => $survey_publication->get_content_object(), self :: PARAM_INVITEE_ID => $this->get_user_id()));
    }

    function get_evaluation_browse_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_EVALUATIONS));
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