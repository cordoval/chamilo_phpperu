<?php

namespace application\weblcms;

use reporting\ReportingManager;
use common\libraries\Toolbar;
use common\libraries\ToolbarItem;
use common\libraries\Theme;
use common\libraries\Translation;
use common\libraries\Utilities;

require_once dirname(__FILE__) . '/catalog_course_browser_table_column_model.class.php';
//require_once// dirname(__FILE__) . '/../../../course/course_table/default_course_table_cell_renderer.class.php';
require_once dirname(__FILE__) . '/../../../course/course.class.php';
require_once dirname(__FILE__) . '/../course_browser/course_browser_table_cell_renderer.class.php';

/**
 * Cell rendere for the learning object browser table
 */
class CatalogCourseBrowserTableCellRenderer extends DefaultCourseTableCellRenderer {

    /**
     * The repository browser component
     */
    private $browser;
    private $target;

    /**
     * Constructor
     * @param WeblcmsBrowserComponent $browser
     */
    function __construct($browser, $target = '') {
        parent :: __construct();
        $this->browser = $browser;
        $this->target = $target;
    }

    // Inherited
    function render_cell($column, $course) {

        if ($column === CatalogCourseBrowserTableColumnModel :: get_modification_column()) {
            return $this->get_modification_links($course);
        }

        // Add special features here
        switch ($column->get_name()) {

            case Course :: PROPERTY_COURSE_TYPE_ID :

                if ($course->get_course_type_id() != 0)
                    return WeblcmsDatamanager :: get_instance()->retrieve_course_type($course->get_course_type_id())->get_name();
                else {
                    return Translation :: get('NoCourseType');
                }

            // Exceptions that need post-processing go here ...
        }
        return parent :: render_cell($column, $course);
    }

    /**
     * Gets the action links to display
     *
     * @param Course $course The course for which the action links should be returned
     * @return string A HTML representation of the action links
     */
    private function get_modification_links($course) {

        $toolbar = new Toolbar(Toolbar :: TYPE_HORIZONTAL);

        if ($url = $this->browser->get_course_viewing_url($course)) {
            $toolbar->add_item(new ToolbarItem(Translation :: get('CourseHome'), Theme :: get_common_image_path() . 'action_home.png', $url, ToolbarItem :: DISPLAY_ICON, false, null, $this->target));
        }
        if ($url = $this->browser->get_course_editing_url($course)) {
            $toolbar->add_item(new ToolbarItem(Translation :: get('Edit', null, Utilities:: COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_edit.png', $url, ToolbarItem :: DISPLAY_ICON, false, null, $this->target));
        }
        if ($url = $this->browser->get_course_deleting_url($course)) {
            $toolbar->add_item(new ToolbarItem(Translation :: get('Delete', null, Utilities:: COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_delete.png', $url, ToolbarItem :: DISPLAY_ICON, true, null, $this->target));
        }
        if ($url = $this->browser->get_course_changing_course_type_url($course)) {
            $toolbar->add_item(new ToolbarItem(Translation :: get('ChangeCourseType'), Theme :: get_common_image_path() . 'action_move.png', $url, ToolbarItem :: DISPLAY_ICON, false, null, $this->target));
        }
        if ($url = $this->browser->get_course_maintenance_url($course)) {
            $toolbar->add_item(new ToolbarItem(Translation :: get('Maintenance'), Theme :: get_common_image_path() . 'action_maintenance.png', $url, ToolbarItem :: DISPLAY_ICON, false, null, $this->target));
        }
        if ($url = $this->browser->get_reporting_url($course)) {
            $toolbar->add_item(new ToolbarItem(Translation :: get('Report', null, 'reporting'), Theme :: get_common_image_path() . 'action_reporting.png', $url, ToolbarItem :: DISPLAY_ICON, false, null, $this->target));
        }

        if ($this->browser->is_subscribed($course, $this->browser->get_user())) {
            //return Translation :: get('AlreadySubscribed');
        } else {
            $course = WeblcmsDataManager :: get_instance()->retrieve_course($course->get_id());
            $current_right = $course->can_user_subscribe($this->browser->get_user());

            switch ($current_right) {
                case CourseGroupSubscribeRight :: SUBSCRIBE_DIRECT :
                    $course_subscription_url = $this->browser->get_course_subscription_url($course);
                    $toolbar->add_item(new ToolbarItem(Translation :: get('Subscribe'), Theme :: get_common_image_path() . 'action_subscribe.png', $course_subscription_url, ToolbarItem :: DISPLAY_ICON, false, null, $this->target));
                    break;

                case CourseGroupSubscribeRight :: SUBSCRIBE_REQUEST :
                    $conditions = array();
                    $date_conditions = array();

                    $conditions[] = new EqualityCondition(CourseRequest :: PROPERTY_COURSE_ID, $course->get_id());
                    $conditions[] = new EqualityCondition(CourseRequest :: PROPERTY_USER_ID, $this->browser->get_user_id());
                    $date_conditions[] = new InequalityCondition(CourseRequest :: PROPERTY_DECISION_DATE, InequalityCondition :: GREATER_THAN_OR_EQUAL, time());
                    $date_conditions[] = new EqualityCondition(CourseRequest :: PROPERTY_DECISION_DATE, NULL);

                    $conditions[] = new OrCondition($date_conditions);
                    $condition = new AndCondition($conditions);

                    $teller = WeblcmsDataManager :: get_instance()->count_requests_by_course($condition);
                    if ($teller == 0) {
                        $course_request_form_url = $this->browser->get_course_request_form_url($course);
                        $toolbar->add_item(new ToolbarItem(Translation :: get('Request'), Theme :: get_common_image_path() . 'action_request.png', $course_request_form_url, ToolbarItem :: DISPLAY_ICON, false, null, $this->target));
                    } else {
                        $toolbar->add_item(new ToolbarItem(Translation :: get('Pending'), Theme :: get_common_image_path() . 'status_pending.png', null, ToolbarItem :: DISPLAY_ICON, false, null, $this->target));
                    }
                    break;

                case CourseGroupSubscribeRight :: SUBSCRIBE_CODE :
                    $course_code_url = $this->browser->get_course_code_url($course);
                    $toolbar->add_item(new ToolbarItem(Translation :: get('Code'), Theme :: get_common_image_path() . 'action_code.png', $course_code_url, ToolbarItem :: DISPLAY_ICON, false, null, $this->target));
                    break;

                default :
                //return Translation :: get('SubscribeNotAllowed');
            }
        }
        return $toolbar->as_html();
    }

}

?>