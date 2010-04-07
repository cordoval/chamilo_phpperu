<?php
/**
 * $Id: learning_path_tool.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.learning_path
 */
require_once dirname(__FILE__) . '/learning_path_tool_component.class.php';
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
        /*$trail = new BreadcrumbTrail();
		$trail->add_help('courses learnpath tool');

		if(!$this->is_allowed(VIEW_RIGHT))
		{
			Display :: not_allowed();
			return;
		}
		if (Request :: get('admin'))
		{
			$_SESSION['wikiadmin'] = Request :: get('admin');
		}
		if ($_SESSION['wikiadmin'] && $this->is_allowed(ADD_RIGHT))
		{
			require_once dirname(__FILE__).'/../../content_object_repo_viewer.class.php';
			$pub = new ContentObjectPublisher($this, 'learning_path');
			$html[] = '<p><a href="' . $this->get_url(array('admin' => 0), array(), true) . '"><img src="'.Theme :: get_common_image_path().'action_browser.png" alt="'.Translation :: get('BrowserTitle').'" style="vertical-align:middle;"/> '.Translation :: get('BrowserTitle').'</a></p>';
			$html[] =  $pub->as_html();
			$this->display_header($trail, true);
			echo implode("\n",$html);
			$this->display_footer();
		}
		else
		{
			$this->display_header($trail, true);
			if($this->is_allowed(ADD_RIGHT))
			{
				echo '<p><a href="' . $this->get_url(array('admin' => 1), array(), true) . '"><img src="'.Theme :: get_common_image_path().'action_publish.png" alt="'.Translation :: get('Publish').'" style="vertical-align:middle;"/> '.Translation :: get('Publish').'</a></p>';
			}
			echo $this->perform_requested_actions();
			$browser = new LearningPathBrowser($this);
			echo $browser->as_html();
			$this->display_footer();
		}*/
    }

    static function get_allowed_types()
    {
        return array('learning_path');
    }
}
?>