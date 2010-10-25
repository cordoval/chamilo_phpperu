<?php
require_once CoreApplication :: get_application_class_lib_path('reporting') .'reporting_block.class.php';

abstract class InternshipOrganizerReportingBlock extends ReportingBlock
{

    public function count_data()
    {
    }

    public function retrieve_data()
    {
    }

    public function get_data_manager()
    {
        return InternshipOrganizerDataManager :: get_instance();
    }

    public function get_available_displaymodes()
    {
        $modes = array();
        //        $modes[ReportingFormatter::DISPLAY_TEXT] = Translation :: get('Text');
        $modes[ReportingFormatter :: DISPLAY_TABLE] = Translation :: get('Table');
        $modes[ReportingChartFormatter :: DISPLAY_PIE] = Translation :: get('Chart:Pie');
        $modes[ReportingChartFormatter :: DISPLAY_BAR] = Translation :: get('Chart:Bar');
        $modes[ReportingChartFormatter :: DISPLAY_LINE] = Translation :: get('Chart:Line');
        $modes[ReportingChartFormatter :: DISPLAY_FILLED_CUBIC] = Translation :: get('Chart:FilledCubic');
        return $modes;
    }

    function get_date($date)
    {
        return date("d-m-Y", $date);
    }
}
?>