<?php
/**
 * $Id: deleter.class.php 193 2009-11-13 11:53:37Z chellee $
 * @package application.lib.assessment.assessment_manager.component
 */
require_once dirname(__FILE__) . '/../assessment_manager.class.php';

/**
 * Component to delete assessment_publications objects
 * @author Sven Vanpoucke
 * @author 
 */
class AssessmentManagerDeleterComponent extends AssessmentManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $ids = Request :: get(AssessmentManager :: PARAM_ASSESSMENT_PUBLICATION);
        $failures = 0;
        
        if (! empty($ids))
        {
            if (! is_array($ids))
            {
                $ids = array($ids);
            }
            
            foreach ($ids as $id)
            {
                $assessment_publication = $this->retrieve_assessment_publication($id);
                if (! $assessment_publication->is_visible_for_target_user($this->get_user()))
                {
                    $failures ++;
                }
                else
                {
	                if(WebApplication :: is_active('gradebook'))
	       			{
	       				require_once dirname(__FILE__) . '/../../../gradebook/gradebook_utilities.class.php';
				    	if(!GradebookUtilities :: move_internal_item_to_external_item(AssessmentManager :: APPLICATION_NAME, $id))
				    		$message = 'failed to move internal evaluation to external evaluation';
	       			}
                    if (! $assessment_publication->delete())
                    {
                        $failures ++;
                    }
                }
            }
            
            if ($failures)
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedAssessmentPublicationDeleted';
                }
                else
                {
                    $message = 'SelectedAssessmentPublicationDeleted';
                }
            }
            else
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedAssessmentPublicationsDeleted';
                }
                else
                {
                    $message = 'SelectedAssessmentPublicationsDeleted';
                }
            }
            
            $this->redirect(Translation :: get($message), ($failures ? true : false), array(AssessmentManager :: PARAM_ACTION => AssessmentManager :: ACTION_BROWSE_ASSESSMENT_PUBLICATIONS));
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoAssessmentPublicationsSelected')));
        }
    }
    
	function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add_help('assessment_deleter');
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(AssessmentManager :: PARAM_ACTION => AssessmentManager :: ACTION_BROWSE_ASSESSMENT_PUBLICATIONS)), Translation :: get('AssessmentManagerBrowserComponent')));
    }
    
    function get_additional_parameters()
    {
    	return array(self :: PARAM_ASSESSMENT_PUBLICATION);
    }
}
?>