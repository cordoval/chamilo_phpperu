<?php
/**
 * $Id: reporting_viewer.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.component
 */

/**
 * Description of reporting_template_viewerclass
 *
 * @author Sven Vanpoucke
 */

class ToolRightsEditorComponent extends ToolComponent
{
    /**
     * Runs this component and displays its output.
     */
    function run()
    {      
        $trail = BreadcrumbTrail :: get_instance();
        $locations = array();
    	
        $course = $this->get_course_id();
        $course_module = $this->get_tool_id();
        $category_id = Request :: get(WeblcmsManager :: PARAM_CATEGORY);
        $publications = Request :: get(WeblcmsManager :: PARAM_PUBLICATION);
        
        if($publications)
        {
        	$type = WeblcmsRights :: TYPE_PUBLICATION;
        	if(!is_array($publications))
        	{
        		$publications = array($publications);
        	}

        	foreach($publications as $publication)
        	{
        		$locations[] = WeblcmsRights :: get_location_by_identifier_from_courses_subtree(WeblcmsRights :: TYPE_PUBLICATION, $publication, $course);
        	}
        }
        else
        {
        	if($category_id)
        	{
        		$locations[] = WeblcmsRights :: get_location_by_identifier_from_courses_subtree(WeblcmsRights :: TYPE_COURSE_CATEGORY, $category_id, $course);
        	}
        	else
        	{
        		if($course_module && $course_module != 'rights')
        		{
        			$course_module = WeblcmsDataManager :: get_instance()->retrieve_course_module_by_name($this->get_course_id(), $this->get_tool_id())->get_id();
        			$locations[] = WeblcmsRights :: get_location_by_identifier_from_courses_subtree(WeblcmsRights :: TYPE_COURSE_MODULE, $course_module, $course);
        		}
        		else
        		{
        			$locations[] = WeblcmsRights :: get_courses_subtree_root($course);
        		}
        	}
        }

        $manager = new RightsEditorManager($this, $locations);
	    $manager->exclude_users(array($this->get_user_id()));
    	$manager->run();
    }
    
    function get_available_rights()
    {
    	return $this->get_parent()->get_available_rights();
    }
}
?>