<?php

namespace application\survey;

use common\libraries\ObjectTableDataProvider;

class SurveyPublicationRelReportingTemplateTableDataProvider extends ObjectTableDataProvider
{

    function SurveyPublicationRelReportingTemplateTableDataProvider($browser, $condition)
    {
        parent :: __construct($browser, $condition);
    }

    function get_objects($offset, $count, $order_property = null)
    {
        $order_property = $this->get_order_property($order_property);
        return SurveyDataManager :: get_instance()->retrieve_survey_publication_rel_reporting_template_registrations($this->get_condition(), $offset, $count, $order_property);
    }

    function get_object_count()
    {
        return SurveyDataManager :: get_instance()->count_survey_publication_rel_reporting_template_registrations($this->get_condition());
    }
}
?>