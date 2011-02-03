<?php

namespace application\weblcms;

use common\libraries\WebApplication;
use common\libraries\Redirect;
use common\libraries\Application;
use common\libraries\CoreApplication;
use common\libraries\Request;
use common\libraries\EqualityCondition;
use common\libraries\AndCondition;
use common\libraries\OrCondition;
use common\libraries\Translation;
use common\libraries\StringUtilities;
use common\libraries\SimpleTemplate;

require_once CoreApplication :: get_application_class_path('weblcms') . 'lib/course/course_category_menu.class.php';
require_once CoreApplication :: get_application_class_path('weblcms') . 'lib/course/course_category_catalog_menu.class.php';
require_once CoreApplication :: get_application_class_path('weblcms') . 'lib/weblcms_manager/component/course_browser/course_browser_table.class.php';
require_once CoreApplication :: get_application_class_path('weblcms') . 'lib/weblcms_manager/component/catalog_course_browser/catalog_course_browser_table.class.php';

/**
 * Block that display the list of all opened courses
 *
 * @copyright (c) 2011 University of Geneva
 * @license GNU General Public License - http://www.gnu.org/copyleft/gpl.html
 * @author lopprecht
 */
class WeblcmsCourseCatalog extends WeblcmsBlock {

    /**
     * Note, with several widgets on the frontpage we may have name clashes for generic terms such as category.
     * For this reason we use 'course_category_id' instead of something more generic such as 'category.
     */
    const PARAM_COURSE_CATEGORY_ID = 'course_category';


    function as_html() {
        $html = array();
        $html[] = $this->display_header();
        $html[] = $this->display_content();
        $html[] = $this->display_footer();
        return implode(StringUtilities::NEW_LINE, $html);
    }

    function display_content(){
        $html = array();
        $html[] = '<div style="padding-top:5px; padding-bottom:15px;font-weight:bold; font-size:15px;">';
        $html[] = '{$CRUMBS}';
        $html[] = '</div>';
        $html[] = '<div style="float: left; width: 20%;"><br/>';
        $html[] = '{$CATEGORY}';
        $html[] = '</div>';
        $html[] = '<div style="float: right; width: 80%; text-align: left">';
        $html[] = '{$COURSE}';
        $html[] = '</div>';

        $category_menu = $this->get_category_menu();
        $CRUMBS = array(array('title' => Translation::get('Home'), 'url' => $this->get_url()));
        $CRUMBS = array_merge($CRUMBS, $category_menu->get_breadcrumbs());
        $CRUMBS = SimpleTemplate::all('<span><a href="{$url}">{$title}</a></span>', $CRUMBS, ' > ');

        $CATEGORY = $category_menu->render_as_tree();
        $table = new CatalogCourseBrowserTable($this, $parameters, $this->get_condition());
        
        $COURSE = $table->as_html();

        return SimpleTemplate::ex($html, get_defined_vars());
    }

    private $_category_menu = null;
    function get_category_menu(){
        if($this->_category_menu){
            return $this->_category_menu;
        }

        $category = Request::get(self::PARAM_COURSE_CATEGORY_ID);
        return $this->_category_menu = new CourseCategoryCatalogMenu($category);
    }

    function get_condition() {
        $category = Request::get(self::PARAM_COURSE_CATEGORY_ID);
        $condition = $category ? new EqualityCondition(Course :: PROPERTY_CATEGORY, $category) : null;

        $visibility_conditions = array();
        $visibility_and_conditions[] = new EqualityCondition(CourseSettings :: PROPERTY_VISIBILITY, 1, CourseSettings :: get_table_name());
        $visibility_and_conditions[] = new EqualityCondition(CourseSettings :: PROPERTY_ACCESS, 1, CourseSettings :: get_table_name());

        //$visibility_or_conditions[] = new EqualityCondition(CourseType :: PROPERTY_ACTIVE, 1, CourseType :: get_table_name());
        //typeless courses are always active. Condition below needed because typeless is not defined in database (no typeless row in CourseType table, so no active property)
        //$visibility_or_conditions[] = new EqualityCondition(Course :: PROPERTY_COURSE_TYPE_ID, '0', Course :: get_table_name());
        //$visibility_and_conditions[] = new OrCondition($visibility_or_conditions);

        $visibility_condition = new AndCondition($visibility_and_conditions);

        $result = empty($condition) ? $visibility_condition : new AndCondition($condition, $visibility_condition);

        return $result;
    }

    function count_courses() {
        $store = Course::get_data_manager();
        $result = $store->count_courses($this->get_condition());
        return $result;
    }

    function retrieve_courses() {
        $store = Course::get_data_manager();
        $result = $store->retrieve_courses($this->get_condition());
        return $result;
    }

    function get_course_subscription_url($course) {
        $params[WeblcmsManager::PARAM_APPLICATION] = WeblcmsManager::APPLICATION_NAME;
        $params[WeblcmsManager :: PARAM_ACTION] = WeblcmsManager :: ACTION_SUBSCRIBE;
        $params[WeblcmsManager :: PARAM_COURSE] = $course->get_id();
        return Redirect::get_link(WeblcmsManager::APPLICATION_NAME, $params);
    }

    function get_course_viewing_url($course) {
        $params[WeblcmsManager::PARAM_APPLICATION] = WeblcmsManager::APPLICATION_NAME;
        $params[WeblcmsManager :: PARAM_ACTION] = WeblcmsManager :: ACTION_VIEW_COURSE;
        $params[WeblcmsManager :: PARAM_COURSE] = $course->get_id();
        return Redirect::get_link(WeblcmsManager::APPLICATION_NAME, $params);
    }

    function get_course_editing_url($course) {
        return '';
    }

    function get_course_deleting_url($course) {
        return '';
    }

    function get_course_changing_course_type_url($course) {
        return '';
    }

    function get_course_maintenance_url() {
        return '';
    }

    function get_reporting_url() {
        return '';
    }

    function is_subscribed($course, $user = null) {
        $user = $user ? $user : $this->get_user();
        $wdm = WeblcmsDataManager :: get_instance();
        return $wdm->is_subscribed($course, $user);
    }

}

?>