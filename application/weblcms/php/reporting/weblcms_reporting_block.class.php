<?php
namespace application\weblcms;

use common\libraries\Path;
use reporting\ReportingBlock;

abstract class WeblcmsReportingBlock extends ReportingBlock
{

    public function get_data_manager()
    {
        return UserDataManager :: get_instance();
    }

    function get_application()
    {
        return WeblcmsManager :: APPLICATION_NAME;
    }
}
?>