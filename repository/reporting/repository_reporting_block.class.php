<?php
require_once Path :: get_reporting_path() . '/lib/reporting_block.class.php';

abstract class RepositoryReportingBlock extends ReportingBlock
{
    public function count_data()
    {
    }

    public function retrieve_data()
    {
    }

    public function get_data_manager()
    {
        return RepositoryDataManager :: get_instance();
    }

    public function get_available_displaymodes()
    {
    }
}
?>