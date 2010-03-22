<?php
/**
 * $Id: admin_course_type_browser.class.php 218 2010-03-11 14:21:26Z Yannick & Tristan $
 * @package application.lib.weblcms.weblcms_manager.component
 */
require_once dirname(__FILE__) . '/../weblcms_manager.class.php';
require_once dirname(__FILE__) . '/../weblcms_manager_component.class.php';
require_once dirname(__FILE__) . '/admin_course_type_browser/admin_course_type_browser_table.class.php';
/**
 * Weblcms component which allows the the platform admin to browse the course_types
 */
class WeblcmsManagerAdminCourseTypeBrowserComponent extends WeblcmsManagerComponent
{
    private $action_bar;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        Header :: set_section('admin');
        
        $trail = new BreadcrumbTrail();
        if ($this->get_user()->is_platform_admin())
        {
            $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('Administration')));
            $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER, 'selected' => WeblcmsManager :: APPLICATION_NAME), array(), false, Redirect :: TYPE_CORE), Translation :: get('CourseTypes')));
        }
        else
        	$trail->add(new Breadcrumb($this->get_url(array(WeblcmsManager :: PARAM_ACTION => null)), Translation :: get('CourseTypes')));
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('CourseTypeList')));
        $trail->add_help('coursetype general');
              
        if (! $this->get_user()->is_platform_admin())
        {
            $this->display_header($trail, false, true);
            Display :: error_message(Translation :: get("NotAllowed"));
            $this->display_footer();
            exit();
        }
        
        $this->display_header($trail, false, true);
        $this->action_bar = $this->get_action_bar();
        echo $this->get_course_type_html();
        $this->display_footer();
    }

    function get_course_type_html()
    {    
        $html = array();
        
        $html[] = '<div style="clear: both;"></div>';
        $html[] = $this->action_bar->as_html() . '<br />';
		$html[] = $this->get_table_html();
        $html[] = '<div style="clear: both;"></div>';
        $html[] = '</div>';
        $html[] = '</div>';
        return implode($html, "\n");
    }
    
	function get_action_bar()
	{
		$action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
		$action_bar->set_search_url($this->get_url());
		$action_bar->add_common_action(new ToolbarItem(Translation :: get('Add'), Theme :: get_common_image_path().'action_add.png', $this->get_url(array(Application :: PARAM_ACTION => WeblcmsManager :: ACTION_ADMIN_COURSE_TYPE_CREATOR)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
		$action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll'), Theme :: get_common_image_path() . 'action_browser.png', $this->get_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
		$action_bar->set_search_url($this->get_url());
		
		return $action_bar;
	}
	  
	function get_table_html()
	{
		$parameters = $this->get_parameters();
		$parameters[WeblcmsManager :: PARAM_ACTION]=  WeblcmsManager :: ACTION_ADMIN_COURSE_TYPE_BROWSER;

		$table = new AdminCourseTypeBrowserTable($this, $parameters, $this->get_condition());

		$html = array();
		$html[] = $table->as_html();

		return implode($html, "\n");
	}
    
    function get_condition()
    {
        $query = $this->action_bar->get_query();
        
        if (isset($query) && $query != '')
        {
            $conditions = array();
            $conditions[] = new PatternMatchCondition(CourseType :: PROPERTY_NAME, '*' . $query . '*');
            $conditions[] = new PatternMatchCondition(CourseType :: PROPERTY_DESCRIPTION, '*' . $query . '*');
            
            $search_conditions = new OrCondition($conditions);
        }       
        $condition = null;
       
        if (count($search_conditions))
       	{
           $condition = $search_conditions;
      	}     
        return $condition;
    }
}
?>
