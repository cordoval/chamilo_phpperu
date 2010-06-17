<?php
/**
 * $Id: course_sections_viewer.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.course_sections.component
 */
require_once dirname(__FILE__) . '/course_sections_browser/course_sections_browser_table.class.php';

class CourseSectionsToolViewerComponent extends CourseSectionsTool
{
    private $action_bar;

    function run()
    {
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add_help('courses sections');
        
        if (! $this->get_course()->is_course_admin($this->get_parent()->get_user()))
        {
            $this->display_header($trail, true);
            Display :: error_message(Translation :: get("NotAllowed"));
            $this->display_footer();
            exit();
        }
        
        $this->action_bar = $this->get_action_bar();
        $table = $this->get_table_html();
        
        $this->display_header($trail, true);
        echo '<br />';
        echo $this->action_bar->as_html();
        echo '<div id="action_bar_browser">';
        echo $table;
        echo '</div>';
        $this->display_footer();
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        
        $action_bar->set_search_url($this->get_url());
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('Create'), Theme :: get_common_image_path() . 'action_publish.png', $this->get_url(array(CourseSectionsTool :: PARAM_ACTION => CourseSectionsTool :: ACTION_CREATE_COURSE_SECTION)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll'), Theme :: get_common_image_path() . 'action_browser.png', $this->get_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        return $action_bar;
    }

    function get_table_html()
    {
        $table = new CourseSectionsBrowserTable($this, array(), $this->get_condition());
        
        $html = array();
        $html[] = '<div style="float: right; width: 100%;">';
        $html[] = $table->as_html();
        $html[] = '</div>';
        
        return implode($html, "\n");
    }

    function get_condition()
    {
        return new EqualityCondition(CourseSection :: PROPERTY_COURSE_CODE, $this->get_course_id());
    }
}
?>