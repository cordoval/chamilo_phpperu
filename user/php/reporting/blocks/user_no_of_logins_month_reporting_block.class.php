<?php
namespace user;

use common\libraries\Translation;
use common\libraries\EqualityCondition;
use common\libraries\AndCondition;
use common\libraries\Utilities;

require_once dirname(__FILE__) . '/../user_reporting_block.class.php';

class UserNoOfLoginsMonthReportingBlock extends UserReportingBlock
{

    public function count_data()
    {
        $reporting_data = new ReportingData();
        require_once (dirname(__FILE__) . '/../../trackers/login_logout_tracker.class.php');
        $conditions = array();
        $conditions[] = new EqualityCondition(LoginLogoutTracker :: PROPERTY_TYPE, 'login');
        $user_id = $this->get_user_id();
        if (isset($user_id))
        {
            $conditions[] = new EqualityCondition(LoginLogoutTracker :: PROPERTY_USER_ID, $user_id);
        }
        $condition = new AndCondition($conditions);

        $data = Tracker :: get_data('login_logout_tracker', UserManager :: APPLICATION_NAME, $condition);

        $months_names = array(
                Translation :: get('JanuaryLong', null, Utilities :: COMMON_LIBRARIES), Translation :: get('FebruaryLong', null, Utilities :: COMMON_LIBRARIES), Translation :: get('MarchLong', null, Utilities :: COMMON_LIBRARIES), Translation :: get('AprilLong', null, Utilities :: COMMON_LIBRARIES), Translation :: get('MayLong', null, Utilities :: COMMON_LIBRARIES), Translation :: get('JuneLong', null, Utilities :: COMMON_LIBRARIES),
                Translation :: get('JulyLong', null, Utilities :: COMMON_LIBRARIES), Translation :: get('AugustLong', null, Utilities :: COMMON_LIBRARIES), Translation :: get('SeptemberLong', null, Utilities :: COMMON_LIBRARIES), Translation :: get('OctoberLong', null, Utilities :: COMMON_LIBRARIES), Translation :: get('NovemberLong', null, Utilities :: COMMON_LIBRARIES), Translation :: get('DecemberLong', null, Utilities :: COMMON_LIBRARIES));
        $months = UserReportingBlock :: getDateArray($data, 'n');

        $reporting_data->set_categories($months_names);
        $reporting_data->set_rows(array(Translation :: get('Logins')));

        foreach ($months_names as $key => $name)
        {
            $reporting_data->add_data_category_row($name, Translation :: get('Logins'), ($months[$key + 1] ? $months[$key + 1] : 0));
        }
        return $reporting_data;
    }

    public function retrieve_data()
    {
        return $this->count_data();
    }

    function get_application()
    {
        return UserManager :: APPLICATION_NAME;
    }

    public function get_available_displaymodes()
    {
        $modes = array();
        //$modes[ReportingFormatter::DISPLAY_TEXT] = Translation :: get('Text');
        $modes[ReportingFormatter :: DISPLAY_TABLE] = Translation :: get('Table', null, 'reporting');
        $modes[ReportingChartFormatter :: DISPLAY_PIE] = Translation :: get('Chart:Pie', null, 'reporting');
        return $modes;
    }
}
?>