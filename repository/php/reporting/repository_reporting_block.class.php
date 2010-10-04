<?php
require_once Path :: get_reporting_path() . '/lib/reporting_block.class.php';

abstract class RepositoryReportingBlock extends ReportingBlock
{
    public function get_data_manager()
    {
        return RepositoryDataManager :: get_instance();
    }
}
?>