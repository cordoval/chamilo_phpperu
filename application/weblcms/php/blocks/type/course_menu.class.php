<?php

namespace application\weblcms;

use repository\ContentObject;
use common\libraries\SubselectCondition;
use repository\RepositoryDataManager;
use common\libraries\WebApplication;
use common\libraries\ObjectTableOrder;
use common\libraries\AndCondition;
use common\libraries\EqualityCondition;
use common\libraries\Path;
use common\libraries\Translation;
use repository\content_object\announcement\Announcement;
use common\libraries\StringUtilities;
use common\libraries\Theme;
use common\libraries\Application;
use common\libraries\PlatformSetting;
use common\libraries\RssIconGenerator;
use common\libraries\Redirect;
use common\libraries\Session;
use common\libraries\SimpleTemplate;

require_once WebApplication :: get_application_class_path('weblcms') . 'blocks/weblcms_block.class.php';
require_once WebApplication :: get_application_class_path('weblcms') . 'lib/weblcms_manager/component/home.class.php';

/**
 * Block that displays main course's actions available in the main course menu. That is create course, register/unregister to course, etc. Do not display less common actions such as manage categories.
 *
 * @copyright (c) 2011 University of Geneva
 * @license GNU General Public License - http://www.gnu.org/copyleft/gpl.html
 * @author lopprecht
 */
class WeblcmsCourseMenu extends WeblcmsBlock {

    function is_teacher() {
        return $this->get_user()->is_teacher() && (Session::get('studentview') != 'studentenview');
    }

    function as_html() {
        $html = array();
        $html[] = $this->display_header();
        $html[] = $this->display_content();
        $html[] = $this->display_footer();

        return implode(StringUtilities::NEW_LINE, $html);
    }

    function display_content() {
        $html = array();
        $html[] = '<div class="tool_menu">';
        $html[] = '<ul>';
        $html[] = '{$ADMIN_MENU}';
        $html[] = '{$USER_MENU}';
        $html[] = '</ul>';
        $html[] = '</div>';

        $template = '<li class="tool_list_menu" style="background-image: url({$IMG})"><a style="top: -3px; position: relative;" href="{$HREF}">{$TEXT}</a></li>';

        $ADMIN_MENU = $this->display_admin_menu($template);
        $USER_MENU = SimpleTemplate::all($template, $this->get_edit_course_menu());

        return SimpleTemplate::ex($html, get_defined_vars());
    }

    function display_admin_menu($template) {
        $result = array();
        if ($this->get_user()->is_platform_admin()) {
            $menu = $this->get_platform_admin_menu();
            $result[] = SimpleTemplate::all($template, $menu);
            $result[] = '<div style="margin: 10px 0 10px 0; border-bottom: 1px dotted #4271B5; height: 0px;"></div>';
        } else {
            if ($menu = $this->get_create_course_menu()) {
                $result[] = SimpleTemplate::all($template, $menu);
            }
        }
        return implode(StringUtilities::NEW_LINE, $result);
    }

    function get_create_course_menu() {
        if (!$this->is_teacher()) {
            return '';
        }

        $result = array();
        $wdm = WeblcmsDataManager :: get_instance();

        $count_direct = count($wdm->retrieve_course_types_by_user_right($this->get_user(), CourseTypeGroupCreationRight :: CREATE_DIRECT));
        if (PlatformSetting :: get('allow_course_creation_without_coursetype', 'weblcms')) {
            $count_direct++;
        }
        if ($count_direct) {
            $HREF = $this->get_course_action_url(WeblcmsManager :: ACTION_CREATE_COURSE);
            $TEXT = htmlspecialchars(Translation :: get('CourseCreate'));
            $IMG = Theme :: get_common_image_path() . 'action_create.png';
            $result[] = compact('HREF', 'TEXT', 'IMG');
        }

        $count_request = count($wdm->retrieve_course_types_by_user_right($this->get_user(), CourseTypeGroupCreationRight :: CREATE_REQUEST));
        if ($count_request) {
            $HREF = $this->get_course_action_url(WeblcmsManager :: ACTION_COURSE_CREATE_REQUEST_CREATOR);
            $TEXT = htmlspecialchars(Translation :: get('CourseRequest'));
            $IMG = Theme :: get_common_image_path() . 'action_create.png';
            $result[] = compact('HREF', 'TEXT', 'IMG');
        }

        return $result;
    }

    function get_edit_course_menu() {
        $result = array();

        $HREF = $this->get_course_action_url(WeblcmsManager :: ACTION_MANAGER_SUBSCRIBE);
        $TEXT = htmlspecialchars(Translation :: get('CourseSubscribe'));
        $IMG = Theme :: get_common_image_path() . 'action_subscribe.png';
        $result[] = compact('HREF', 'TEXT', 'IMG');

        $HREF = $this->get_course_action_url(WeblcmsManager :: ACTION_MANAGER_UNSUBSCRIBE);
        $TEXT = htmlspecialchars(Translation :: get('CourseUnsubscribe'));
        $IMG = Theme :: get_common_image_path() . 'action_unsubscribe.png';
        $result[] = compact('HREF', 'TEXT', 'IMG');

        $HREF = htmlspecialchars(RssIconGenerator :: generate_rss_url(WeblcmsManager :: APPLICATION_NAME, 'publication', $this->get_user()));
        $TEXT = htmlspecialchars(Translation :: get('RssFeed'));
        $IMG = Theme::get_content_object_image_path('rss_feed');
        $result[] = compact('HREF', 'TEXT', 'IMG');

        return $result;
    }

    function get_platform_admin_menu() {
        $result = array();

        $HREF = $this->get_course_action_url(WeblcmsManager :: ACTION_CREATE_COURSE);
        $TEXT = htmlspecialchars(Translation :: get('CourseCreate'));
        $IMG = Theme :: get_common_image_path() . 'action_create.png';
        $result[] = compact('HREF', 'TEXT', 'IMG');

        $HREF = $this->get_course_action_url(WeblcmsManager :: ACTION_ADMIN_COURSE_BROWSER);
        $TEXT = htmlspecialchars(Translation :: get('CourseList'));
        $IMG = Theme :: get_common_image_path() . 'action_browser.png';
        $result[] = compact('HREF', 'TEXT', 'IMG');

        $HREF = $this->get_course_action_url(WeblcmsManager :: ACTION_ADMIN_REQUEST_BROWSER);
        $TEXT = htmlspecialchars(Translation :: get('RequestList'));
        $IMG = Theme :: get_common_image_path() . 'action_browser.png';
        $result[] = compact('HREF', 'TEXT', 'IMG');

        return $result;
    }
    
    function get_course_action_url($action) {
        $params[WeblcmsManager::PARAM_APPLICATION] = WeblcmsManager::APPLICATION_NAME;
        $params[WeblcmsManager :: PARAM_ACTION] = $action;
        return htmlspecialchars(Redirect::get_link(WeblcmsManager::APPLICATION_NAME, $params));
    }

}

?>
