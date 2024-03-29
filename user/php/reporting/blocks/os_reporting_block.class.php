<?php
namespace user;

use reporting\ReportingChartFormatter;
use reporting\ReportingFormatter;
use reporting\Reporting;
use reporting\ReportingData;

use common\libraries\Translation;
use common\libraries\EqualityCondition;

require_once dirname(__FILE__) . '/../user_reporting_block.class.php';

class OsReportingBlock extends UserReportingBlock
{

    public function count_data()
    {
        $reporting_data = new ReportingData();
        require_once (dirname(__FILE__) . '/../../trackers/os_tracker.class.php');
        $tracker = new OSTracker();
        $condition = new EqualityCondition(OSTracker :: PROPERTY_TYPE, 'os');
        $description[0] = Translation :: get('Os');

        $data = Reporting :: array_from_tracker($tracker, $condition, $description);
        $keys = array_keys($data);
        $reporting_data->set_categories($keys);
        $reporting_data->set_rows(array(Translation :: get('Os')));

        foreach ($keys as $key => $name)
        {
            $reporting_data->add_data_category_row($name, Translation :: get('Os'), $data[$name]);
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