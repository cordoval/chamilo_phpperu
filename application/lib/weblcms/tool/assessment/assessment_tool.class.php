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
    const ACTION_VIEW_RESULTS = 'result';
    const ACTION_SAVE_DOCUMENTS = 'save_documents';
    const ACTION_EXPORT_RESULTS = 'export_results';
    const ACTION_DELETE_RESULTS = 'delete_results';
    const ACTION_EXPORT_QTI = 'export_qti';
    const ACTION_IMPORT_QTI = 'import_qti';

    const ACTION_DELETE_PUBLICATION = 'delete_pub';
    const ACTION_VIEW_ASSESSMENTS = 'view';
    const ACTION_VIEW_USER_ASSESSMENTS = 'view_user';
    const ACTION_PUBLISH_SURVEY = 'publish_survey';
    const ACTION_VIEW = 'view';

    const PARAM_USER_ASSESSMENT = 'uaid';
    const PARAM_QUESTION_ATTEMPT = 'qaid';
    const PARAM_ASSESSMENT = 'aid';
    const PARAM_ANONYMOUS = 'anonymous';
    const PARAM_INVITATION_ID = 'invitation_id';
    const PARAM_PUBLICATION_ACTION = 'publication_action';

    /*
	 * Inherited.
	 */
    function run()
    {
        $action = $this->get_action();

        switch ($action)
        {
            case self :: ACTION_VIEW :
                $component = $this->create_component('Viewer');
                break;
            case self :: ACTION_BROWSE :
                $component = $this->create_component('Browser');
                break;
            case self :: ACTION_PUBLISH :
                $component = $this->create_component('Publisher');
                break;
            case self :: ACTION_UPDATE :
                $component = $this->create_component('Updater');
                break;
            case self :: ACTION_DELETE :
                $component = $this->create_component('Deleter');
                break;
            case self :: ACTION_TOGGLE_VISIBILITY :
                $component = $this->create_component('ToggleVisibility');
                break;
            case self :: ACTION_MOVE_UP :
                $component = $this->create_component('MoveUp');
                break;
            case self :: ACTION_MOVE_DOWN :
                $component = $this->create_component('MoveDown');
                break;
            case self :: ACTION_PUBLISH_INTRODUCTION :
                $component = $this->create_component('IntroductionPublisher');
                break;
            case self :: ACTION_VIEW_REPORTING_TEMPLATE :
                $component = $this->create_component('ReportingViewer');
                break;
            case self :: ACTION_BUILD_COMPLEX_CONTENT_OBJECT :
                $component = $this->create_component('Builder');
                break;
            case self :: ACTION_MOVE_TO_CATEGORY :
                $component = $this->create_component('CategoryMover');
                break;
            case self :: ACTION_MANAGE_CATEGORIES :
                $component = $this->create_component('CategoryManager');
                break;
            case self :: ACTION_DISPLAY_COMPLEX_CONTENT_OBJECT :
                $component = $this->create_component('Taker');
                break;
            case self :: ACTION_VIEW_RESULTS :
                $component = $this->create_component('ResultsViewer');
                break;
            case self :: ACTION_SAVE_DOCUMENTS :
                $component = $this->create_component('DocumentSaver');
                break;
            case self :: ACTION_DELETE_RESULTS :
                $component = $this->create_component('ResultsDeleter');
                break;
            case self :: ACTION_EXPORT_RESULTS :
                $component = $this->create_component('ResultsExport');
                break;
            case self :: ACTION_EXPORT_QTI :
                $component = $this->create_component('QtiExporter');
                break;
            case self :: ACTION_IMPORT_QTI :
                $component = $this->create_component('QtiImporter');
                break;
            case self :: ACTION_SHOW_PUBLICATION:
            	$component = $this->create_component('ShowPublication');
                break;
            case self :: ACTION_HIDE_PUBLICATION:
            	$component = $this->create_component('HidePublication');
                break;
            default :
                $component = $this->create_component('Browser');
        }

        $component->run();
    }

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
        
        if($this->is_allowed(EDIT_RIGHT))
        {
        	$extra_toolbar_items[] = new ToolbarItem(Translation :: get('Export'), Theme :: get_common_image_path() . 'action_export.png', $this->get_url(array(AssessmentTool :: PARAM_ACTION => AssessmentTool :: ACTION_EXPORT_QTI, Tool :: PARAM_PUBLICATION_ID => $publication->get_id())), ToolbarItem :: DISPLAY_ICON);
        }

        return $extra_toolbar_items;
    }
    
    private static $checked_publications = array();
    
    function is_content_object_attempt_possible($publication)
    {  
    	if(!array_key_exists($publication->get_id(), self :: $checked_publications))
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
}
?>