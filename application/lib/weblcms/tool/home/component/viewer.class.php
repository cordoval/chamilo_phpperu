<?php
class HomeToolViewerComponent extends HomeTool
{

    function run()
    {
        $trail = BreadcrumbTrail :: get_instance();
        $this->set_parameter(Tool :: PARAM_PUBLICATION_ID, null);
        $this->set_parameter('tool_action', null);
        $this->set_parameter('course_group', null);

        $title = CourseLayout :: get_title($this->get_course());

        if (Request :: get('previous') == 'admin')
        {
            $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('Administration')));
            $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER, DynamicTabsRenderer :: PARAM_SELECTED_TAB => WeblcmsManager :: APPLICATION_NAME), array(), false, Redirect :: TYPE_CORE), Translation :: get('Courses')));
        }
        else
        {
            $trail->add(new Breadcrumb($this->get_url(array('go' => null, 'course' => null)), Translation :: get('MyCourses')));
        }
        $trail->add(new Breadcrumb($this->get_url(), $title));
        $trail->add_help('courses general');

        $wdm = WeblcmsDataManager :: get_instance();

        $this->display_header($trail, false, true);

        //Display menu
        $menu_style = $this->get_course()->get_menu();
        if ($menu_style != CourseLayout :: MENU_OFF)
        {
            $renderer = ToolListRenderer :: factory('Menu', $this);
            $renderer->display();
            echo '<div id="tool_browser_' . ($renderer->display_menu_icons() && ! $renderer->display_menu_text() ? 'icon_' : '') . $renderer->get_menu_style() . '">';
        }
        else
        {
            echo '<div id="tool_browser">';
        }
        if ($this->get_course()->get_intro_text())
        {
            echo $this->display_introduction_text();
            echo '<div class="clear"></div>';
        }

        $renderer = ToolListRenderer :: factory('FixedLocation', $this);
        $renderer->display();
        echo '</div>';
        $this->display_footer();
        $wdm->log_course_module_access($this->get_course_id(), $this->get_user_id(), 'course_home');
//
//        $this->display_header();
//        dump($this->get_parent()->get_course());
//        echo 'Homepage goes here';
//        $this->display_footer();
    }

    function get_registered_tools()
    {
        return $this->get_parent()->get_registered_tools();
    }

    function tool_has_new_publications($tool_name)
    {
        return $this->get_parent()->tool_has_new_publications($tool_name);
    }
}
?>