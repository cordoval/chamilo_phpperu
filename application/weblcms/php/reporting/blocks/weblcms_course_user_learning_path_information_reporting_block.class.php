<?php
namespace application\weblcms;

use reporting\ReportingData;
use reporting\ReportingFormatter;
use common\libraries\Path;
use common\libraries\Translation;

require_once dirname(__FILE__) . '/../weblcms_course_reporting_block.class.php';
require_once Path :: get_reporting_path() . '/lib/reporting_data.class.php';
class WeblcmsCourseUserLearningPathInformationReportingBlock extends WeblcmsCourseReportingBlock
{

    public function count_data()
    {
        $reporting_data = new ReportingData();

        return $reporting_data;
    }

    public function retrieve_data()
    {
        return $this->count_data();
    }

    public function get_available_displaymodes()
    {
        $modes = array();
        //$modes[ReportingFormatter::DISPLAY_TEXT] = Translation :: get('Text');
        $modes[ReportingFormatter :: DISPLAY_TABLE] = Translation :: get('Table');
        return $modes;
    }
}
?>