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

require_once WebApplication :: get_application_class_path('weblcms') . 'blocks/weblcms_block.class.php';
require_once WebApplication :: get_application_class_path('weblcms') . 'lib/weblcms_manager/component/home.class.php';

/**
 * Block that displays main course's actions available in the main course menu. That is create course, register/unregister to course, etc. Do not display less common actions such as manage categories.
 *
 * @copyright (c) 2011 University of Geneva
 * @license GNU General Public License - http://www.gnu.org/copyleft/gpl.html
 * @author lopprecht
 */
class WeblcmsMenu extends WeblcmsBlock {

    function is_teacher(){
        return $this->get_user()->is_teacher() && (Session::get('studentview') != 'studentenview');
    }

    function as_html() {
        $html = array();
        $html[] = $this->display_header();
        $html[] = $this->display_menu();
        $html[] = $this->display_footer();

        return implode(StringUtilities::NEW_LINE, $html);
    }

    public function f(){
    }

    function display_menu() {
        $html = array();
        $html[] = '<div class="tool_menu">';
        $html[] = '<ul>';
        $html[] = '{$ADMIN_MENU}';
        $html[] = '{$USER_MENU}';
        $html[] = '</ul>';
        $html[] = '</div>';
        $html = implode(StringUtilities::NEW_LINE, $html);

        $ADMIN_MENU = array();
        if ($this->get_user()->is_platform_admin()) {
            $ADMIN_MENU[] = $this->display_platform_admin_course_list_links();
            $ADMIN_MENU[] = '<div style="margin: 10px 0 10px 0; border-bottom: 1px dotted #4271B5; height: 0px;"></div>';
        } else {
            if ($display = $this->display_create_course_link()) {
                $ADMIN_MENU[] = $display;
            }
        }
        $ADMIN_MENU = implode(StringUtilities::NEW_LINE, $ADMIN_MENU);
        $USER_MENU = $this->display_edit_course_list_links();

        $html = str_replace('{$ADMIN_MENU}', $ADMIN_MENU, $html);
        $html = str_replace('{$USER_MENU}', $USER_MENU, $html);

        return $html;
    }

    function display_create_course_link() {
        if (! $this->is_teacher()) {
            return '';
        }

        $html = array();
        $wdm = WeblcmsDataManager :: get_instance();

        $count_direct = count($wdm->retrieve_course_types_by_user_right($this->get_user(), CourseTypeGroupCreationRight :: CREATE_DIRECT));
        if (PlatformSetting :: get('allow_course_creation_without_coursetype', 'weblcms')) {
            $count_direct++;
        }
        if ($count_direct) {
            $href = $this->get_course_action_url(WeblcmsManager :: ACTION_CREATE_COURSE);
            $text = htmlspecialchars(Translation :: get('CourseCreate'));
            $img = 'action_create.png';
            $html[] = $this->display_item($text, $href, $img);
        }

        $count_request = count($wdm->retrieve_course_types_by_user_right($this->get_user(), CourseTypeGroupCreationRight :: CREATE_REQUEST));
        if ($count_request) {
            $href = $this->get_course_action_url(WeblcmsManager :: ACTION_COURSE_CREATE_REQUEST_CREATOR);
            $text = htmlspecialchars(Translation :: get('CourseRequest'));
            $img = 'action_create.png';
            $html[] = $this->display_item($text, $href, $img);
        }

        return implode($html, StringUtilities::NEW_LINE);
    }

    function display_edit_course_list_links() {
        $html = array();

        $href = $this->get_course_action_url(WeblcmsManager :: ACTION_MANAGER_SUBSCRIBE);
        $text = htmlspecialchars(Translation :: get('CourseSubscribe'));
        $img = 'action_subscribe.png';
        $html[] = $this->display_item($text, $href, $img);

        $href = $this->get_course_action_url(WeblcmsManager :: ACTION_MANAGER_UNSUBSCRIBE);
        $text = htmlspecialchars(Translation :: get('CourseUnsubscribe'));
        $img = 'action_unsubscribe.png';
        $html[] = $this->display_item($text, $href, $img);

        $href = htmlspecialchars(RssIconGenerator :: generate_rss_url(WeblcmsManager :: APPLICATION_NAME, 'publication', $this->get_user()));
        $text = htmlspecialchars(Translation :: get('RssFeed'));
        $img = Theme::get_content_object_image_path('rss_feed');

        $html[] = $this->display_item($text, $href, $img, false);

        return implode(StringUtilities::NEW_LINE, $html);
    }

    function display_platform_admin_course_list_links() {
        $html = array();

        $href = $this->get_course_action_url(WeblcmsManager :: ACTION_CREATE_COURSE);
        $text = htmlspecialchars(Translation :: get('CourseCreate'));
        $img = 'action_create.png';
        $html[] = $this->display_item($text, $href, $img);

        $href = $this->get_course_action_url(WeblcmsManager :: ACTION_ADMIN_COURSE_BROWSER);
        $text = htmlspecialchars(Translation :: get('CourseList'));
        $img = 'action_browser.png';
        $html[] = $this->display_item($text, $href, $img);

        $href = $this->get_course_action_url(WeblcmsManager :: ACTION_ADMIN_REQUEST_BROWSER);
        $text = htmlspecialchars(Translation :: get('RequestList'));
        $img = 'action_browser.png';
        $html[] = $this->display_item($text, $href, $img);

        return implode(StringUtilities::NEW_LINE, $html);
    }

    function display_item($text, $href, $img, $common_image = true) {
        $result = '<li class="tool_list_menu" style="background-image: url({$IMG})"><a style="top: -3px; position: relative;" href="{$HREF}">{$TEXT}</a></li>';

        if ($common_image) {
            $img = htmlspecialchars(Theme :: get_common_image_path()) . $img;
        }

        $result = str_replace('{$IMG}', $img, $result);
        $result = str_replace('{$HREF}', $href, $result);
        $result = str_replace('{$TEXT}', $text, $result);
        return $result;
    }

    function get_course_action_url($action) {
        $params[WeblcmsManager::PARAM_APPLICATION] = WeblcmsManager::APPLICATION_NAME;
        $params[WeblcmsManager :: PARAM_ACTION] = $action;
        return htmlspecialchars(Redirect::get_link(WeblcmsManager::APPLICATION_NAME, $params));
    }

}

?>
