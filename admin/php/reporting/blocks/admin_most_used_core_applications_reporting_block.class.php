<?php
namespace admin;
use common\libraries\Utilities;
use common\libraries\CoreApplication;
use common\libraries\Translation;
use common\libraries\Path;
use common\libraries\PatternMatchCondition;

use reporting\ReportingData;
use reporting\ReportingFormatter;

use tracking\Tracker;

use user\VisitTracker;
use user\UserManager;

require_once dirname(__FILE__) . '/../admin_reporting_block.class.php';
require_once Path :: get_admin_path() . 'lib/admin_manager/admin_manager.class.php';

class AdminMostUsedCoreApplicationsReportingBlock extends AdminReportingBlock
{

    public function count_data()
    {
        $reporting_data = new ReportingData();
        $reporting_data->set_rows(array(Translation :: get('NumberVisit')));

        require_once Path :: get_user_path() . 'trackers/visit_tracker.class.php';

        $applications = CoreApplication :: get_list();
        foreach ($applications as $application)
        {
            $condition = new PatternMatchCondition(VisitTracker :: PROPERTY_LOCATION, '*application=' . $application . '*');
            $count = Tracker :: count_data('visit_tracker', UserManager :: APPLICATION_NAME, $condition);

            $reporting_data->add_category($application);
            $reporting_data->add_data_category_row($application, Translation :: get('Number', array(), Utilities :: COMMON_LIBRARIES), $count);
        }
        return $reporting_data;
    }

    public function retrieve_data()
    {
        return $this->count_data();
    }

    public function get_application()
    {
        return AdminManager :: APPLICATION_NAME;
    }

    public function get_available_displaymodes()
    {
        $modes = array();
        $modes[ReportingFormatter :: DISPLAY_TABLE] = Translation :: get('Table', array(), Utilities :: COMMON_LIBRARIES);
        return $modes;
    }
}
?>