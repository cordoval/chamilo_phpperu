<?php
require_once dirname(__FILE__) . '/../user_reporting_block.class.php';

class UserNoOfUsersReportingBlock extends UserReportingBlock
{

    public function count_data()
    {
        $udm = UserDataManager :: get_instance();
        $reporting_data = new ReportingData();

        $reporting_data->set_categories(array(Translation :: get('getNumberOfUsers')));
        $reporting_data->set_rows(array(Translation :: get('count')));

        $reporting_data->add_data_category_row(Translation :: get('getNumberOfUsers'), Translation :: get('count'), $udm->count_users());

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
        $modes[ReportingFormatter :: DISPLAY_TABLE] = Translation :: get('Table');
        return $modes;
    }
}
?>