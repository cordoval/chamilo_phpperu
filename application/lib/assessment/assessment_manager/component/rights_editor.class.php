<?php
/**
 * $Id: rights_editor.class.php 198 2009-11-13 12:20:22Z vanpouckesven $
 * @package webservices.lib.webservice_manager.component
 */

/**
 * Repository manager component to edit the rights for the learning objects in
 * the repository.
 */
class AssessmentManagerRightsEditorComponent extends AssessmentManager implements DelegateComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $category = Request :: get(AssessmentManager :: PARAM_CATEGORY);
    	$publications = Request :: get(AssessmentManager :: PARAM_ASSESSMENT_PUBLICATION);

        if ($publications && ! is_array($publications))
        {
            $publications = array($publications);
        }

        $locations = array();

        foreach ($publications as $publication)
        {
        	if ($this->get_user()->is_platform_admin() || $publication->get_publisher() == $this->get_user_id())
        	{ 
        		$locations[] = AssessmentRights :: get_location_by_identifier_from_assessments_subtree($publication, AssessmentRights :: TYPE_PUBLICATION);
        	}
        }

        if(count($locations) == 0)
        {
        	if ($this->get_user()->is_platform_admin())
        	{
        		if($category == 0)
        		{
        			$locations[] = AssessmentRights :: get_assessments_subtree_root();
        		}
        		else
        		{
        			$locations[] = AssessmentRights :: get_location_by_identifier_from_assessments_subtree($category, AssessmentRights :: TYPE_CATEGORY);
        		}
        	}
        }
        
        $manager = new RightsEditorManager($this, $locations);
	    $manager->exclude_users(array($this->get_user_id()));
    	$manager->run();
    }
    
	function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add_help('assessment_rights_editor');
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(AssessmentManager :: PARAM_ACTION => AssessmentManager :: ACTION_BROWSE_ASSESSMENT_PUBLICATIONS)), Translation :: get('AssessmentManagerBrowserComponent')));
    }
    
    function get_additional_parameters()
    {
    	return array(self :: PARAM_ASSESSMENT_PUBLICATION, self :: PARAM_CATEGORY);
    }
    
    function get_available_rights()
    {
    	$publications = Request :: get(AssessmentManager :: PARAM_ASSESSMENT_PUBLICATION);
    	if(count($publications) > 0)
    	{
    		return AssessmentRights :: get_available_rights_for_publications();
    	}
    	
    	return AssessmentRights :: get_available_rights_for_categories();
    }

}
?>