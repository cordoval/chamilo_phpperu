<?php
namespace admin;
use common\libraries\WebApplication;
use common\libraries\Translation;
use common\libraries\Path;
use common\libraries\PatternMatchCondition;

use user\VisitTracker;
use user\UserManager;

use tracking\Tracker;

use reporting\ReportingData;
use reporting\ReportingFormatter;

require_once dirname(__FILE__) . '/../admin_reporting_block.class.php';
require_once Path :: get_admin_path() . 'lib/admin_manager/admin_manager.class.php';

class AdminMostUsedWebApplicationsReportingBlock extends AdminReportingBlock
{

    public function count_data()
    {
        $reporting_data = new ReportingData();
        $reporting_data->set_rows(array(Translation :: get('NumberVisit')));

        require_once PATH :: get_user_path() . 'trackers/visit_tracker.class.php';

        $applications = WebApplication :: load_all(false);
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