<?php
namespace application\weblcms;

use common\libraries\PatternMatchCondition;
use common\libraries\ActionBarRenderer;
use common\libraries\ToolbarItem;
use admin\AdminManager;
use common\libraries\Redirect;
use common\libraries\Theme;
use common\libraries\DynamicTabsRenderer;
use common\libraries\OrCondition;
use common\libraries\Breadcrumb;
use common\libraries\BreadcrumbTrail;
use common\libraries\AndCondition;
use common\libraries\EqualityCondition;
use common\libraries\Request;
use common\libraries\Path;
use common\libraries\Translation;
use common\libraries\Utilities;

/**
 * $Id: course_type_viewer.class.php 218 2010-03-26 14:21:26Z Yannick & Tristan $
 * @package application.lib.weblcms.weblcms_manager.component
 */
require_once dirname(__FILE__) . '/admin_course_browser/admin_course_browser_table.class.php';
require_once Path :: get_library_path() . 'html/action_bar/action_bar_renderer.class.php';

class WeblcmsManagerCourseTypeViewerComponent extends WeblcmsManager
{

    private $course_type;
    private $ab;

    function run()
    {
        $breadcrumbtrail = BreadcrumbTrail :: get_instance();

        $id = Request :: get(WeblcmsManager :: PARAM_COURSE_TYPE);
        if ($id)
        {
            $this->course_type = $this->retrieve_course_type($id);
            $course_type = $this->course_type;

            $this->display_header();
            $this->ab = $this->get_action_bar();

            echo $this->get_browser_html($course_type);

            $this->display_footer();
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoCourseTypeSelected')));
        }
    }

    function get_browser_html($course_type)
    {
        $html = array();
        $html[] = '<div>';
        $html[] = $this->ab->as_html() . '<br />';
        $html[] = '<div class="clear"></div><div class="content_object" style="background-image: url(' . Theme :: get_common_image_path() . 'place_group.png);">';
        $html[] = '<div class="title">' . Translation :: get('Description', null, Utilities :: COMMON_LIBRARIES ) . '</div>';
        $html[] = $this->course_type->get_description();
        $html[] = '</div>';
        $html[] = '<div class="content_object" style="background-image: url(' . Theme :: get_common_image_path() . 'place_publications.png);">';
        $html[] = '<div class="title">' . Translation :: get('Courses') . '</div>';
        $html[] = $this->get_table_html();
        $html[] = '</div>';
        $html[] = '<div style="clear: both;"></div>';
        $html[] = '</div>';
        return implode("\n", $html);
    }

    function get_table_html()
    {
        $parameters = $this->get_parameters();
        $parameters[WeblcmsManager :: PARAM_ACTION] = WeblcmsManager :: ACTION_VIEW_COURSE_TYPE;
        $table = new AdminCourseBrowserTable($this, $parameters, $this->get_condition());
        $html = array();
        $html[] = $table->as_html();

        return implode("\n", $html);
    }

    function get_condition()
    {
        $conditions = array();

        $query = $this->ab->get_query();

        if (isset($query) && $query != '')
        {
            $conditions = array();
            $conditions[] = new PatternMatchCondition(Course :: PROPERTY_NAME, '*' . $query . '*');
            $conditions[] = new PatternMatchCondition(Course :: PROPERTY_VISUAL, '*' . $query . '*');
            $conditions[] = new PatternMatchCondition(CourseSettings :: PROPERTY_LANGUAGE, '*' . $query . '*', CourseSettings :: get_table_name());

            $search_conditions = new OrCondition($conditions);
        }

        $condition = null;

        if (isset($this->category))
        {
            $condition = new EqualityCondition(Course :: PROPERTY_CATEGORY, $this->category);

            if (count($search_conditions))
            {
                $condition = new AndCondition($condition, $search_conditions);
            }
        }
        else
        {
            if (count($search_conditions))
            {
                $condition = $search_conditions;
            }
        }

        $course_type_condition = new EqualityCondition(Course :: PROPERTY_COURSE_TYPE_ID, $this->course_type->get_id());
        if (! is_null($condition))
            $condition = new AndCondition($condition, $course_type_condition);
        else
            $condition = $course_type_condition;

        return $condition;
    }

    function get_action_bar()
    {
        $course_type = $this->course_type;

        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

        $action_bar->set_search_url($this->get_url(array(WeblcmsManager :: PARAM_COURSE_TYPE => $course_type->get_id())));
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('Edit', null, Utilities :: COMMON_LIBRARIES ), Theme :: get_common_image_path() . 'action_edit.png', $this->get_course_type_editing_url($course_type), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('Delete', null, Utilities :: COMMON_LIBRARIES ), Theme :: get_common_image_path() . 'action_delete.png', $this->get_course_type_deleting_url($course_type), ToolbarItem :: DISPLAY_ICON_AND_LABEL, true));
        $action_bar->add_tool_action(new ToolbarItem(Translation :: get('DeleteAllCourses'), Theme :: get_common_image_path() . 'action_delete.png', $this->get_course_type_deleting_all_courses_url($course_type), ToolbarItem :: DISPLAY_ICON_AND_LABEL, true));
        return $action_bar;
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {

        if ($this->get_user()->is_platform_admin())
        {
            $breadcrumbtrail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('TypeName', null, 'admin')));
            $breadcrumbtrail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER, DynamicTabsRenderer :: PARAM_SELECTED_TAB => WeblcmsManager :: APPLICATION_NAME), array(), false, Redirect :: TYPE_CORE), Translation :: get('Courses')));
            $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(WeblcmsManager :: PARAM_ACTION => WeblcmsManager :: ACTION_ADMIN_COURSE_TYPE_BROWSER)), Translation :: get('CourseTypeList')));
        }
        else
        {
            $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(WeblcmsManager :: PARAM_ACTION => WeblcmsManager :: ACTION_VIEW_WEBLCMS_HOME)), Translation :: get('CourseType')));
            $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(WeblcmsManager :: PARAM_ACTION => WeblcmsManager :: ACTION_ADMIN_COURSE_TYPE_BROWSER)), Translation :: get('CourseTypeList')));
        }
    }

    function get_additional_parameters()
    {
        return array();
    }

}

?>
