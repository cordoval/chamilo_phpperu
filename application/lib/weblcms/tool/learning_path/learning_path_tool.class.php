<?php
/**
 * $Id: learning_path_tool.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.learning_path
 */
/**
 * This tool allows a user to publish learning paths in his or her course.
 */
class LearningPathTool extends Tool
{
    const ACTION_VIEW_LEARNING_PATH = 'view';
    const ACTION_BROWSE_LEARNING_PATHS = 'browse';
    const ACTION_EXPORT_SCORM = 'exp_scorm';
    const ACTION_IMPORT_SCORM = 'import';
    const ACTION_VIEW_STATISTICS = 'stats';
    const ACTION_VIEW_CLO = 'view_clo';
    const ACTION_VIEW_ASSESSMENT_CLO = 'view_assessment_clo';
    const ACTION_VIEW_DOCUMENT = 'view_document';
    
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
            case Tool :: ACTION_DELETE :
                $component = LearningPathToolComponent :: factory('Deleter', $this);
                break;
        }
        
        if (! $component)
        {
            $component = parent :: run();
            if ($component)
                return;
            
            switch ($action)
            {
                case self :: ACTION_PUBLISH :
                    $component = LearningPathToolComponent :: factory('Publisher', $this);
                    break;
                case self :: ACTION_VIEW_LEARNING_PATH :
                    $component = LearningPathToolComponent :: factory('Viewer', $this);
                    break;
                case self :: ACTION_BROWSE_LEARNING_PATHS :
                    $component = LearningPathToolComponent :: factory('Browser', $this);
                    break;
                case self :: ACTION_EXPORT_SCORM :
                    $component = LearningPathToolComponent :: factory('ScormExporter', $this);
                    break;
                case self :: ACTION_VIEW_STATISTICS :
                    $component = LearningPathToolComponent :: factory('StatisticsViewer', $this);
                    break;
                case self :: ACTION_IMPORT_SCORM :
                    $component = LearningPathToolComponent :: factory('ScormImporter', $this);
                    break;
                case self :: ACTION_VIEW_CLO :
                    $component = LearningPathToolComponent :: factory('CloViewer', $this);
                    break;
                case self :: ACTION_VIEW_ASSESSMENT_CLO :
                    $component = LearningPathToolComponent :: factory('AssessmentCloViewer', $this);
                    break;
                case self :: ACTION_VIEW_DOCUMENT :
                    $component = LearningPathToolComponent :: factory('DocumentViewer', $this);
                    break;
                default :
                    $component = LearningPathToolComponent :: factory('Browser', $this);
                    break;
            }
        }
        
        $component->run();
    }

    static function get_allowed_types()
    {
        return array(LearningPath :: get_type_name());
    }
    
	function get_application_component_path()
	{
		return dirname(__FILE__) . '/component/';
	}
}
?>