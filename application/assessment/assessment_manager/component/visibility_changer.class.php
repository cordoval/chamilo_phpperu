<?php
/**
 * $Id: visibility_changer.class.php 193 2009-11-13 11:53:37Z chellee $
 * @package application.lib.assessment.assessment_manager.component
 */
require_once dirname(__FILE__) . '/../assessment_manager.class.php';

/**
 * Component to create a new assessment_publication object
 * @author Sven Vanpoucke
 * @author 
 */
class AssessmentManagerVisibilityChangerComponent extends AssessmentManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $pid = Request :: get(self :: PARAM_ASSESSMENT_PUBLICATION);
        
        if ($pid)
        {
            $publication = $this->retrieve_assessment_publication($pid);
            
            if (! $publication->is_visible_for_target_user($this->get_user()))
            {
                $this->redirect(null, false, array(AssessmentManager :: PARAM_ACTION => AssessmentManager :: ACTION_BROWSE_ASSESSMENT_PUBLICATIONS));
            }
            
            $publication->toggle_visibility();
            $succes = $publication->update();
            
            $message = $succes ? 'VisibilityChanged' : 'VisibilityNotChanged';
            
            $this->redirect(Translation :: get($message), ! $succes, array(AssessmentManager :: PARAM_ACTION => AssessmentManager :: ACTION_BROWSE_ASSESSMENT_PUBLICATIONS));
        }
        else
        {
            $this->redirect(Translation :: get('NoPublicationSelected'), true, array(AssessmentManager :: PARAM_ACTION => AssessmentManager :: ACTION_BROWSE_ASSESSMENT_PUBLICATIONS));
        }
    }
    
	function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add_help('assessment_visibility_changer');
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(AssessmentManager :: PARAM_ACTION => AssessmentManager :: ACTION_BROWSE_ASSESSMENT_PUBLICATIONS)), Translation :: get('AssessmentManagerBrowserComponent')));
    }
    
    function get_additional_parameters()
    {
    	return array(self :: PARAM_ASSESSMENT_PUBLICATION);
    }
}
?>