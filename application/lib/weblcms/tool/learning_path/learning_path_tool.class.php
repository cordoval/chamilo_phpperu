<?php
/**
 * $Id: learning_path_tool.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.learning_path
 */
/**
 * This tool allows a user to publish learning paths in his or her course.
 */
class LearningPathTool extends Tool implements Categorizable
{
    const ACTION_EXPORT_SCORM = 'exp_scorm';
    const ACTION_IMPORT_SCORM = 'import';
    const ACTION_VIEW_STATISTICS = 'stats';
    const ACTION_VIEW_CLO = 'view_clo';
    const ACTION_VIEW_ASSESSMENT_CLO = 'view_assessment_clo';
    const ACTION_VIEW_DOCUMENT = 'view_document';
    const ACTION_ATTEMPT = 'attempt';
    
    const PARAM_LEARNING_PATH = 'lp';
    const PARAM_LP_STEP = 'step';
    const PARAM_LEARNING_PATH_ID = 'lpid';
    const PARAM_ATTEMPT_ID = 'attempt_id';

    // Inherited.
    function run()
    {
        $action = $this->get_action();
        switch ($action)
        {
        	case self :: ACTION_PUBLISH :
                $component = $this->create_component('Publisher');
                break;
            case self :: ACTION_VIEW :
                $component = $this->create_component('Viewer');
                break;
            case self :: ACTION_BROWSE :
                $component = $this->create_component('Browser');
                break;
            case self :: ACTION_EXPORT_SCORM :
                $component = $this->create_component('ScormExporter');
                break;
            case self :: ACTION_VIEW_STATISTICS :
                $component = $this->create_component('StatisticsViewer');
                break;
            case self :: ACTION_IMPORT_SCORM :
                $component = $this->create_component('ScormImporter');
                break;
            case self :: ACTION_VIEW_CLO :
                $component = $this->create_component('CloViewer');
                break;
            case self :: ACTION_VIEW_ASSESSMENT_CLO :
                $component = $this->create_component('AssessmentCloViewer');
                break;
            case self :: ACTION_VIEW_DOCUMENT :
                $component = $this->create_component('DocumentViewer');
                break;
            case self :: ACTION_UPDATE:
            	$component = $this->create_component('Updater');
                break;
            case self :: ACTION_DELETE:
            	$component = $this->create_component('Deleter');
                break;
            case self :: ACTION_TOGGLE_VISIBILITY:
            	$component = $this->create_component('ToggleVisibility');
                break;
            case self :: ACTION_MOVE_DOWN:
            	$component = $this->create_component('MoveDown');
                break;
            case self :: ACTION_MOVE_UP:
            	$component = $this->create_component('MoveUp');
                break;
            case self :: ACTION_BUILD_COMPLEX_CONTENT_OBJECT:
            	$component = $this->create_component('ComplexBuilder');
                break;
            case self :: ACTION_PUBLISH_INTRODUCTION:
            	$component = $this->create_component('IntroductionPublisher');
                break;
            case self :: ACTION_ATTEMPT:
            	$component = $this->create_component('Attempt');
                break;
            default :
                $component = $this->create_component('Browser');
                break;
        }
        
        $component->run();
    }
	
    function get_available_browser_types()
    {
        $browser_types = array();
        $browser_types[] = ContentObjectPublicationListRenderer :: TYPE_TABLE;
        $browser_types[] = ContentObjectPublicationListRenderer :: TYPE_LIST;
        return $browser_types;
    }
    
    static function get_allowed_types()
    {
        return array(LearningPath :: get_type_name());
    }
    
	function get_application_component_path()
	{
		return dirname(__FILE__) . '/component/';
	}
	
	function get_content_object_publication_actions($publication)
    {
        $object = $publication->get_content_object_id();
        $condition = new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $object);
        $count = RepositoryDataManager :: get_instance()->count_complex_content_object_items($condition);

        if($count > 0)
        {
	    	$items[] = new ToolbarItem(
	        		Translation :: get('AttemptLearningPath'),
	        		Theme :: get_common_image_path() . 'action_start.png',
	        		$this->get_url(array(Tool :: PARAM_ACTION => LearningPathTool :: ACTION_ATTEMPT, Tool :: PARAM_PUBLICATION_ID => $publication->get_id())),
	        		ToolbarItem :: DISPLAY_ICON
	        );
	        
	        $items[] = new ToolbarItem(
	        		Translation :: get('Statistics'),
	        		Theme :: get_common_image_path() . 'action_statistics.png',
	        		$this->get_url(array(Tool :: PARAM_ACTION => LearningPathTool :: ACTION_VIEW_STATISTICS, Tool :: PARAM_PUBLICATION_ID => $publication->get_id())),
	        		ToolbarItem :: DISPLAY_ICON
       	 	);
        }
        else
        {
        	$items[] = new ToolbarItem(
	        		Translation :: get('AttemptLearningPathNA'),
	        		Theme :: get_common_image_path() . 'action_right_na.png',
					null,
	        		ToolbarItem :: DISPLAY_ICON
	        );
	        
	        $items[] = new ToolbarItem(
	        		Translation :: get('StatisticsNA'),
	        		Theme :: get_common_image_path() . 'action_statistics_na.png',
					null,
	        		ToolbarItem :: DISPLAY_ICON
	        );
        }
        
       return $items;
    }
}
?>