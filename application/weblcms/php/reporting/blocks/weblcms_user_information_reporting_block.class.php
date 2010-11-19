<?php
namespace application\weblcms;

use admin\OnlineTracker;
use reporting\ReportingData;
use reporting\ReportingFormatter;
use user\UserDataManager;
use common\libraries\Path;
use common\libraries\Translation;

require_once dirname(__FILE__) . '/../weblcms_tool_reporting_block.class.php';
require_once Path :: get_reporting_path() . '/lib/reporting_data.class.php';
class WeblcmsUserInformationReportingBlock extends WeblcmsToolReportingBlock
{

    public function count_data()
    {
        $reporting_data = new ReportingData();
        $uid = $this->get_user_id();

        require_once Path :: get_admin_path() . '/trackers/online_tracker.class.php';
        $udm = UserDataManager :: get_instance();
        $tracking = new OnlineTracker();

        $items = $tracking->retrieve_tracker_items();
        foreach ($items as $item)
        {
            if ($item->get_user_id() == $uid)
            {
                $online = 1;
            }
        }

        $user = $udm->retrieve_user($uid);

        $reporting_data->set_categories(array(Translation :: get('Name'), Translation :: get('Email'), Translation :: get('Phone'), Translation :: get('Online')));
        $reporting_data->set_rows(array(Translation :: get('count')));

        $reporting_data->add_data_category_row(Translation :: get('Name'), Translation :: get('count'), $user->get_fullname());
        $reporting_data->add_data_category_row(Translation :: get('Email'), Translation :: get('count'), '<a href="mailto:' . $user->get_email() . '" >' . $user->get_email() . '</a>');
        $reporting_data->add_data_category_row(Translation :: get('Phone'), Translation :: get('count'), $user->get_phone());
        $reporting_data->add_data_category_row(Translation :: get('Online'), Translation :: get('count'), ($online) ? Translation :: get('Online') : Translation :: get('Offline'));

        return $reporting_data;
    }

    public function retrieve_data()
    {
        return $this->count_data();
    }

    public function get_available_displaymodes()
    {
        $modes = array();
        $modes[ReportingFormatter :: DISPLAY_TABLE] = Translation :: get('Table');
        return $modes;
    }
}
?>