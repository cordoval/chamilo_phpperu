<?php
/**
 * $Id: browser.class.php 191 2009-11-13 11:50:28Z chellee $
 * @package application.common.rights_editor_manager.component
 */

require_once Path :: get_common_extensions_path() . 'rights_editor_manager/component/browser.class.php';
require_once dirname(__FILE__) . '/location_course_group_browser/location_course_group_browser_table.class.php';
require_once dirname(__FILE__) . '/../../tool/course_group/course_group_menu.class.php';

class CoursesRightsEditorManagerBrowserComponent extends RightsEditorManagerBrowserComponent
{

    function get_display_html()
    {
        $html = array();
        
        $html[] = $this->display_type_selector();
        $html[] = $this->action_bar->as_html() . '<br />';
        $html[] = $this->display_locations();
        
        if ($this->type == self :: TYPE_USER)
        {
            $html[] = $this->display_location_user_browser();
        }
        else
        {
            //$html[] = $this->display_location_group_browser();
            $html[] = $this->display_location_course_group_browser();
        }
        
        $html[] = '<div class="clear"></div><br />';
        $html[] = RightsUtilities :: get_rights_legend();
        
        return implode("\n", $html);
    }

    function display_location_course_group_browser()
    {
        $html = array();
        
        $renderer_name = Utilities :: camelcase_to_underscores(get_class($this));
        $tabs = new DynamicTabsRenderer($renderer_name);
        
        $html[] = '<div style="float: left; width: 18%; overflow: auto;">';
        
        $course_group = Request :: get(CoursesRightsEditorManager :: PARAM_COURSE_GROUP) ? Request :: get(CoursesRightsEditorManager :: PARAM_COURSE_GROUP) : 1;
        
        $url = $this->get_parent()->get_url(array(self :: PARAM_TYPE => 'group')) . '&course=%s&course_group=%s';
        $course_group_menu = new CourseGroupMenu($this->get_parent()->get_course(), $course_group, $url);
        $html[] = $course_group_menu->render_as_tree();
        
        $html[] = '</div>';
        $html[] = '<div style="float: right; width: 80%;">';
        
        $course_group_object = WeblcmsDataManager :: get_instance()->retrieve_course_group($course_group);
        if ($course_group_object->has_children())
        {
            $table = new LocationCourseGroupBrowserTable($this, $this->get_parameters(), $this->get_group_conditions());
            $tabs->add_tab(new DynamicContentTab(self :: TAB_SUBGROUPS, Translation :: get('Subgroups'), Theme :: get_image_path('admin') . 'place_mini_group.png', $table->as_html()));
        }
        
        $table = new LocationCourseGroupBrowserTable($this, $this->get_parameters(), $this->get_group_conditions(false));
        $tabs->add_tab(new DynamicContentTab(self :: TAB_DETAILS, Translation :: get('Rights'), Theme :: get_image_path('admin') . 'place_mini_rights.png', $table->as_html()));
        
        $html[] = $tabs->render();
        $html[] = '</div>';
        $html[] = ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PATH) . 'application/weblcms/php/courses_rights_editor/javascript/configure_course_group.js');
        
        return implode("\n", $html);
    }

    function get_course_group_conditions($get_children = true)
    {
        $conditions = array();
        
        $query = $this->action_bar->get_query();
        if (isset($query) && $query != '')
        {
            $conditions[] = new PatternMatchCondition(CourseGroup :: PROPERTY_NAME, '*' . $query . '*');
        }
        
        $course_group = Request :: get(CoursesRightsEditorManager :: PARAM_COURSE_GROUP) ? Request :: get(CoursesRightsEditorManager :: PARAM_COURSE_GROUP) : 1;
        if ($get_children)
        {
            $conditions[] = new EqualityCondition(CourseGroup :: PROPERTY_PARENT_ID, $course_group);
        }
        else
        {
            $conditions[] = new EqualityCondition(CourseGroup :: PROPERTY_ID, $course_group);
        }
        
        return new AndCondition($conditions);
    }
}
?>