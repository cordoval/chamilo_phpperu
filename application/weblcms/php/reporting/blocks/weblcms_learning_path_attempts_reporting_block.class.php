<?php
namespace application\weblcms;

use reporting\ReportingData;
use reporting\ReportingFormatter;
use reporting\ReportingManager;
use user\UserDataManager;
use common\libraries\Redirect;
use common\libraries\Theme;
use common\libraries\AndCondition;
use common\libraries\EqualityCondition;
use common\libraries\Path;
use common\libraries\Translation;
use application\weblcms\tool\learning_path\LearningPathTool;
use application\weblcms\tool\learning_path\LearningPathToolStatisticsViewerComponent;
use common\libraries\Text;

require_once dirname(__FILE__) . '/../weblcms_tool_reporting_block.class.php';
require_once Path :: get_reporting_path() . '/lib/reporting_data.class.php';

class WeblcmsLearningPathAttemptsReportingBlock extends WeblcmsToolReportingBlock
{

    public function count_data()
    {
        $reporting_data = new ReportingData();
        $reporting_data->set_rows(array(Translation :: get('Progress'), Translation :: get('Details')));

        $data = array();

        $pid = $this->get_pid();
        $course_id = $this->get_course_id();
        $tool = $this->get_tool();

        $conditions[] = new EqualityCondition(WeblcmsLpAttemptTracker :: PROPERTY_COURSE_ID, $course_id);
        $conditions[] = new EqualityCondition(WeblcmsLpAttemptTracker :: PROPERTY_LP_ID, $pid);
        $condition = new AndCondition($conditions);

        $udm = UserDataManager :: get_instance();

        $dummy = new WeblcmsLpAttemptTracker();
        $trackers = $dummy->retrieve_tracker_items($condition);

        foreach ($trackers as $tracker)
        {
            $params = $this->get_parent()->get_parameters();
            $params[LearningPathTool :: PARAM_ATTEMPT_ID] = $tracker->get_id();

            $url = Redirect :: get_url($params, array(ReportingManager :: PARAM_TEMPLATE_ID));

            $params[LearningPathToolStatisticsViewerComponent :: PARAM_STAT] = LearningPathToolStatisticsViewerComponent :: ACTION_DELETE_LP_ATTEMPT;
            $delete_url = Redirect :: get_url($params);

            $user = $udm->retrieve_user($tracker->get_user_id());
            $data[Translation :: get('User', null, 'user')] = $user->get_fullname();
            $data[Translation :: get('Progress')] = $tracker->get_progress() . '%';
            //$action = '<a href="' . $url . '">' . Theme :: get_common_image('action_reporting') . '</a>';
            $action = Text :: create_link($url, Theme :: get_common_image('action_reporting')) . ' ' . Text :: create_link($delete_url, Theme :: get_common_image('action_delete'));

            $reporting_data->add_category($user->get_fullname());
            $reporting_data->add_data_category_row($user->get_fullname(), Translation :: get('Progress'), $tracker->get_progress() . '%');
            $reporting_data->add_data_category_row($user->get_fullname(), Translation :: get('Details'), $action);
        }
        return $reporting_data;
    }

    public function retrieve_data()
    {
        return $this->count_data();
    }

    public function get_available_displaymodes()
    {
        $modes = array();
        //$modes[ReportingFormatter::DISPLAY_TEXT] = Translation :: get('Text');
        $modes[ReportingFormatter :: DISPLAY_TABLE] = Translation :: get('Table');
        return $modes;
    }
}
?>