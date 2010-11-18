<?php

namespace application\survey;
use common\libraries\ObjectTableDataProvider;

class SurveyReportingTemplateTableDataProvider extends ObjectTableDataProvider
{

    function SurveyReportingTemplateTableDataProvider($browser, $condition)
    {
        parent :: __construct($browser, $condition);
    }

    function get_objects($offset, $count, $order_property = null)
    {
        $order_property = $this->get_order_property($order_property);
        return ReportingDataManager :: get_instance()->retrieve_reporting_template_registrations($this->get_condition(), $offset, $count, $order_property);
    }

    function get_object_count()
    {
        return ReportingDataManager :: get_instance()->count_reporting_template_registrations($this->get_condition());
    }
}
?>