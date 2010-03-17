<?php
/**
 * $Id: admin_course_type_browser.class.php 218 2009-11-13 14:21:26Z Yannick & Tristan $
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
        //$trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_APPLICATION => 'weblcms')), Translation :: get('MyCourseTypes')));
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
            echo '<div class="clear"></div><br />';
            Display :: error_message(Translation :: get("NotAllowed"));
            $this->display_footer();
            exit();
        }
        
        $this->action_bar = $this->get_action_bar();
        $menu = $this->get_menu_html();
        $output = $this->get_course_html();
        
        $this->display_header($trail, false, true);
        echo '<div class="clear"></div>';
        echo '<br />' . $this->action_bar->as_html() . '<br />';
        echo $menu;
        echo $output;
        $this->display_footer();
    }

	function get_action_bar()
	{
		$action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
		$action_bar->set_search_url($this->get_url());
		$action_bar->add_common_action(new ToolbarItem(Translation :: get('Add'), Theme :: get_common_image_path().'action_add.png', $this->get_url(array(Application :: PARAM_ACTION => WeblcmsManager :: ACTION_ADMIN_COURSE_TYPE_CREATOR)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
		$action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll'), Theme :: get_common_image_path() . 'action_browser.png', $this->get_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));

		return $action_bar;
	}

    function get_course_html()
    {
        $table = new AdminCourseTypeBrowserTable($this, null, $this->get_condition());
        
        $html = array();
        $html[] = '<div style="float: right; width: 100%;">';
        $html[] = $table->as_html();
        $html[] = '</div>';
        
        return implode($html, "\n");
    }
	
    function get_menu_html()
    {
        $extra_items = array();
        if ($this->get_search_validate())
        {
            // $search_url = $this->get_url();
            $search_url = '#';
            $search = array();
            $search['title'] = Translation :: get('SearchResults');
            $search['url'] = $search_url;
            $search['class'] = 'search_results';
            $extra_items[] = $search;
        }
        else
        {
            $search_url = null;
        }
        
        $url_format = $this->get_url(array(Application :: PARAM_ACTION => WeblcmsManager :: ACTION_ADMIN_COURSE_TYPE_BROWSER, WeblcmsManager));
        $url_format = str_replace($temp_replacement, '%s', $url_format);
        
        $html = array();
        $html[] = '<div style="float: left; width: 20%;">';
        $html[] = '</div>';
        
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
