<?php
namespace user;

use reporting\ReportingData;
use reporting\ReportingFormatter;

use common\libraries\Translation;

require_once dirname(__FILE__) . '/../user_reporting_block.class.php';

class UserNoOfUsersReportingBlock extends UserReportingBlock
{

    public function count_data()
    {
        $udm = UserDataManager :: get_instance();
        $reporting_data = new ReportingData();

        $reporting_data->set_categories(array(Translation :: get('GetNumberOfUsers')));
        $reporting_data->set_rows(array(Translation :: get('Count')));

        $reporting_data->add_data_category_row(Translation :: get('GetNumberOfUsers'), Translation :: get('Count'), $udm->count_users());

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
        $modes[ReportingFormatter :: DISPLAY_TABLE] = Translation :: get('Table', null, 'reporting');
        return $modes;
    }
}
?>