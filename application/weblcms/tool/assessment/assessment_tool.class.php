<?php
/**
 * $Id: assessment_tool.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.assessment
 */
require_once Path :: get_application_path() . 'lib/weblcms/tool/tool.class.php';
require_once Path :: get_application_path() . 'lib/weblcms/trackers/weblcms_assessment_attempts_tracker.class.php';
/**
 * This tool allows a user to publish assessments in his or her course.
 */
class AssessmentTool extends Tool implements Categorizable
{
    const ACTION_VIEW_RESULTS = 'results_viewer';
    const ACTION_SAVE_DOCUMENTS = 'document_saver';
    const ACTION_EXPORT_RESULTS = 'results_export';
    const ACTION_DELETE_RESULTS = 'results_deleter';
    const ACTION_EXPORT_QTI = 'qti_exporter';
    const ACTION_IMPORT_QTI = 'qti_importer';
    const ACTION_TAKE_ASSESSMENT = 'complex_display';
    
    const PARAM_USER_ASSESSMENT = 'uaid';
    const PARAM_QUESTION_ATTEMPT = 'qaid';
    const PARAM_ASSESSMENT = 'aid';
    const PARAM_ANONYMOUS = 'anonymous';
    const PARAM_INVITATION_ID = 'invitation_id';
    const PARAM_PUBLICATION_ACTION = 'publication_action';

    static function get_allowed_types()
    {
        return array(Assessment :: get_type_name(), Hotpotatoes :: get_type_name());
    }

    function get_application_component_path()
    {
        return dirname(__FILE__) . '/component/';
    }

    function get_available_browser_types()
    {
        $browser_types = array();
        $browser_types[] = ContentObjectPublicationListRenderer :: TYPE_TABLE;
        $browser_types[] = ContentObjectPublicationListRenderer :: TYPE_LIST;
        $browser_types[] = ContentObjectPublicationListRenderer :: TYPE_CALENDAR;
        return $browser_types;
    }

    function get_content_object_publication_actions($publication)
    {
        $extra_toolbar_items = array();
        
        $extra_toolbar_items[] = new ToolbarItem(Translation :: get('ViewResults'), Theme :: get_common_image_path() . 'action_view_results.png', $this->get_url(array(Tool :: PARAM_ACTION => AssessmentTool :: ACTION_VIEW_RESULTS, AssessmentTool :: PARAM_ASSESSMENT => $publication->get_id())), ToolbarItem :: DISPLAY_ICON);
        
        if ($this->is_allowed(WeblcmsRights :: EDIT_RIGHT))
        {
            $extra_toolbar_items[] = new ToolbarItem(Translation :: get('Export'), Theme :: get_common_image_path() . 'action_export.png', $this->get_url(array(AssessmentTool :: PARAM_ACTION => AssessmentTool :: ACTION_EXPORT_QTI, Tool :: PARAM_PUBLICATION_ID => $publication->get_id())), ToolbarItem :: DISPLAY_ICON);
        }
        
        return $extra_toolbar_items;
    }
    
    private static $checked_publications = array();

    function is_content_object_attempt_possible($publication)
    {
        if (! array_key_exists($publication->get_id(), self :: $checked_publications))
        {
            $assessment = $publication->get_content_object();
            $track = new WeblcmsAssessmentAttemptsTracker();
            $condition_t = new EqualityCondition(WeblcmsAssessmentAttemptsTracker :: PROPERTY_ASSESSMENT_ID, $publication->get_id());
            $condition_u = new EqualityCondition(WeblcmsAssessmentAttemptsTracker :: PROPERTY_USER_ID, $this->get_user_id());
            $condition = new AndCondition(array($condition_t, $condition_u));
            $trackers = $track->retrieve_tracker_items($condition);
            
            $count = count($trackers);
            
            foreach ($trackers as $tracker)
            {
                if ($tracker->get_status() == 'not attempted')
                {
                    $this->active_tracker = $tracker;
                    $count --;
                    break;
                }
            }
            
            self :: $checked_publications[$publication->get_id()] = ($assessment->get_maximum_attempts() == 0 || $count < $assessment->get_maximum_attempts());
        }
        
        return self :: $checked_publications[$publication->get_id()];
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
}
?>