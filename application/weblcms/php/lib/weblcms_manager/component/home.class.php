<?php
namespace application\weblcms;

use common\libraries\RssIconGenerator;
use common\libraries\PlatformSetting;
use common\libraries\Application;
use common\libraries\Theme;
use common\libraries\Session;
use common\libraries\BreadcrumbTrail;
use common\libraries\Request;
use common\libraries\Path;
use common\libraries\DelegateComponent;
use common\libraries\Translation;
use common\libraries\LocalSetting;

/**
 * $Id: home.class.php 218 2009-11-13 14:21:26Z kariboe $
 * @package application.lib.weblcms.weblcms_manager.component
 */
require_once dirname(__FILE__) . '/../../course/course_list_renderer/course_type_course_list_renderer.class.php';
require_once dirname(__FILE__) . '/../../course/course_list_renderer/open_course_type_course_list_renderer.class.php';
require_once dirname(__FILE__) . '/../../course/course_list_renderer/open_closed_course_type_course_list_renderer.class.php';
require_once dirname(__FILE__) . '/../../course/course_user_category.class.php';
/**
 * Weblcms component which provides the user with a list
 * of all courses he or she has subscribed to.
 */
class WeblcmsManagerHomeComponent extends WeblcmsManager implements DelegateComponent
{
    const VIEW_MIXED = 0;
    const VIEW_OPEN_CLOSED = 1;
    const VIEW_OPEN = 2;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = BreadcrumbTrail :: get_instance();

        $this->message = Request :: get('message');
        Request :: set_get('message', null);

        $view_state = LocalSetting :: get('view_state', WeblcmsManager :: APPLICATION_NAME);

        switch ($view_state)
        {
            case self :: VIEW_MIXED :
                $renderer = new CourseTypeCourseListRenderer($this);
                break;
            case self :: VIEW_OPEN_CLOSED :
                $renderer = new OpenClosedCourseTypeCourseListRenderer($this);
                break;
            case self :: VIEW_OPEN :
                $renderer = new OpenCourseTypeCourseListRenderer($this);
                break;
        }

        $renderer->show_new_publication_icons();
        $html[] = $renderer->as_html();
        $html[] = '<script type="text/javascript" src="' . htmlspecialchars(Path :: get(WEB_LIB_PATH)) . 'libraries/resources/javascript/home_ajax.js' . '"></script>';
        $toolbar_state = Session :: retrieve('toolbar_state');
        if ($toolbar_state == 'hide')
        {
            $html[] = '<script type="text/javascript">var hide = "true";</script>';
        }
        else
        {
            $html[] = '<script type="text/javascript">var hide = "false";</script>';
        }

        $this->display_header();
        echo '<div class="clear"></div>';

        echo $this->display_menu();

        echo '<div id="tool_browser_right">';
        echo implode("\n", $html);
        echo '</div>';

        $this->display_footer();
    }

    function display_menu()
    {
        $html = array();

        $html[] = '<div id="tool_bar" class="tool_bar tool_bar_right">';

        $img_path = htmlspecialchars(Theme :: get_common_image_path());
        $html[] = '<div id="tool_bar_hide_container" class="hide">';
        $html[] = '<a id="tool_bar_hide" href="#"><img src="' . $img_path . 'action_action_bar_right_hide.png" /></a>';
        $html[] = '<a id="tool_bar_show" href="#"><img src="' . $img_path . 'action_action_bar_right_show.png" /></a>';
        $html[] = '</div>';

        $html[] = '<div class="tool_menu">';
        $html[] = '<ul>';

        if ($this->get_user()->is_platform_admin())
        {
            $html[] = '<li class="tool_list_menu title" style="font-weight: bold">' . htmlspecialchars(Translation :: get('CourseManagement')) . '</li><br />';
            $html[] = $this->display_platform_admin_course_list_links();
            $html[] = '<div style="margin: 10px 0 10px 0; border-bottom: 1px dotted #4271B5; height: 0px;"></div>';
        }
        else
        {
            $display_add_course_link = $this->get_user()->is_teacher() && ($_SESSION["studentview"] != "studentenview");
            if ($display_add_course_link)
            {
                if ($display = $this->display_create_course_link())
                {
                    $html[] = '<li class="tool_list_menu" style="font-weight: bold">' . htmlspecialchars(Translation :: get('MenuUser')) . '</li><br />';
                    $html[] = $display;
                }
            }
        }

        $html[] = '<li class="tool_list_menu title" style="font-weight: bold">' . htmlspecialchars(Translation :: get('UserCourseManagement')) . '</li><br />';
        $html[] = $this->display_edit_course_list_links();
        $html[] = '</ul>';
        $html[] = '</div>';

        $html[] = '</div>';
        $html[] = '<script type="text/javascript" src="' . htmlspecialchars(Path :: get(WEB_LIB_PATH)) . 'libraries/resources/javascript/tool_bar.js' . '"></script>';
        $html[] = '<div class="clear"></div>';
        return implode($html, "\n");
    }

    function display_create_course_link()
    {
        $html = array();
        $wdm = WeblcmsDataManager :: get_instance();

        $img_path = htmlspecialchars(Theme :: get_common_image_path());
        
        $count_direct = count($wdm->retrieve_course_types_by_user_right($this->get_user(), CourseTypeGroupCreationRight :: CREATE_DIRECT));
        if (PlatformSetting :: get('allow_course_creation_without_coursetype', 'weblcms'))
        {
            $count_direct ++;
        }
        if ($count_direct)
        {
            $html[] = '<li class="tool_list_menu" style="background-image: url(' . $img_path . 'action_create.png)"><a style="top: -3px; position: relative;" href="' . htmlspecialchars($this->get_url(array(Application :: PARAM_ACTION => WeblcmsManager :: ACTION_CREATE_COURSE))) . '">' . htmlspecialchars(Translation :: get('CourseCreate')) . '</a></li>';
        }

        $count_request = count($wdm->retrieve_course_types_by_user_right($this->get_user(), CourseTypeGroupCreationRight :: CREATE_REQUEST));
        if ($count_request)
        {
            $html[] = '<li class="tool_list_menu" style="background-image: url(' . $img_path . 'action_create.png)"><a style="top: -3px; position: relative;" href="' . htmlspecialchars($this->get_url(array(Application :: PARAM_ACTION => WeblcmsManager :: ACTION_COURSE_CREATE_REQUEST_CREATOR))) . '">' . htmlentitites(Translation :: get('CourseRequest')) . '</a></li>';
        }

        if ($count_direct + $count_request)
        {
            return implode("\n", $html);
        }
        else
        {
            return false;
        }
    }

    function display_edit_course_list_links()
    {
        $html = array();
        $img_path = htmlspecialchars(Theme :: get_common_image_path());
        $html[] = '<li class="tool_list_menu" style="background-image: url(' . $img_path . 'action_reset.png)"><a style="top: -3px; position: relative;" href="' . htmlspecialchars($this->get_url(array(Application :: PARAM_ACTION => WeblcmsManager :: ACTION_MANAGER_SORT))) . '">' . htmlspecialchars(Translation :: get('SortMyCourses')) . '</a></li>';

        if (PlatformSetting :: get('show_subscribe_button_on_course_home', 'weblcms'))
        {
            $html[] = '<li class="tool_list_menu" style="background-image: url(' . $img_path . 'action_subscribe.png)"><a style="top: -3px; position: relative;" href="' . htmlspecialchars($this->get_url(array(Application :: PARAM_ACTION => WeblcmsManager :: ACTION_MANAGER_SUBSCRIBE))) . '">' . htmlspecialchars(Translation :: get('CourseSubscribe')) . '</a></li>';
            $html[] = '<li class="tool_list_menu" style="background-image: url(' . $img_path . 'action_unsubscribe.png)"><a style="top: -3px; position: relative;" href="' . htmlspecialchars($this->get_url(array(Application :: PARAM_ACTION => WeblcmsManager :: ACTION_MANAGER_UNSUBSCRIBE))) . '">' . htmlspecialchars(Translation :: get('CourseUnsubscribe')) . '</a></li>';
        }

        $html[] = '<li class="tool_list_menu" style="background-image: url(' . $img_path . 'treemenu_types/rss_feed.png)"><a style="top: -3px; position: relative;" href="' . htmlspecialchars(RssIconGenerator :: generate_rss_url(WeblcmsManager :: APPLICATION_NAME, 'publication', $this->get_user())) . '">' . htmlspecialchars(Translation :: get('RssFeed')) . '</a></li>';
        return implode($html, "\n");
    }

    function display_platform_admin_course_list_links()
    {
        $html = array();
        $img_path = htmlspecialchars(Theme :: get_common_image_path());
        $html[] = '<li class="tool_list_menu" style="background-image: url(' . $img_path . 'action_create.png)"><a style="top: -3px; position: relative;" href="' . htmlspecialchars($this->get_url(array(Application :: PARAM_ACTION => WeblcmsManager :: ACTION_CREATE_COURSE))) . '">' . htmlspecialchars(Translation :: get('CourseCreate')) . '</a></li>';
        $html[] = '<li class="tool_list_menu" style="background-image: url(' . $img_path . 'action_browser.png)"><a style="top: -3px; position: relative;" href="' . htmlspecialchars($this->get_url(array(Application :: PARAM_ACTION => WeblcmsManager :: ACTION_ADMIN_COURSE_BROWSER))) . '">' . htmlspecialchars(Translation :: get('CourseList')) . '</a></li>';
        $html[] = '<li class="tool_list_menu" style="background-image: url(' . $img_path . 'action_browser.png)"><a style="top: -3px; position: relative;" href="' . htmlspecialchars($this->get_url(array(Application :: PARAM_ACTION => WeblcmsManager :: ACTION_ADMIN_REQUEST_BROWSER))) . '">' . htmlspecialchars(Translation :: get('RequestList')) . '</a></li>';
        $html[] = '<li class="tool_list_menu" style="background-image: url(' . $img_path . 'action_move.png)"><a style="top: -3px; position: relative;" href="' . htmlspecialchars($this->get_url(array(Application :: PARAM_ACTION => WeblcmsManager :: ACTION_COURSE_CATEGORY_MANAGER))) . '">' . htmlspecialchars(Translation :: get('CourseCategoryManagement')) . '</a></li>';
        $html[] = '<li class="tool_list_menu" style="background-image: url(' . $img_path . 'action_add.png)"><a style="top: -3px; position: relative;" href="' . htmlspecialchars($this->get_url(array(Application :: PARAM_ACTION => WeblcmsManager :: ACTION_IMPORT_COURSES))) . '">' . htmlspecialchars(Translation :: get('ImportCourseCSV')) . '</a></li>';
        $html[] = '<li class="tool_list_menu" style="background-image: url(' . $img_path . 'action_add.png)"><a style="top: -3px; position: relative;" href="' . htmlspecialchars($this->get_url(array(Application :: PARAM_ACTION => WeblcmsManager :: ACTION_IMPORT_COURSE_USERS))) . '">' . htmlspecialchars(Translation :: get('ImportUsersForCourseCSV')) . '</a></li>';

        return implode($html, "\n");
    }

    function get_course_user_category_actions(CourseUserCategory $course_user_category, CourseType $course_type, $offset, $count)
    {
        $img_path = htmlspecialchars(Theme :: get_common_image_path());
        return '<a href="#" class="closeEl"><img class="visible" src="' . $img_path . 'action_visible.png"/><img class="invisible" style="display: none;" src="' . $img_path . 'action_invisible.png" /></a>';
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add_help('weblcms_course_home');
    }

    function get_additional_parameters()
    {
        return array();
    }
}
?>