<?php

class SurveyContextRegistrationBrowserTableDataProvider extends ObjectTableDataProvider
{

    function SurveyContextRegistrationBrowserTableDataProvider($browser, $condition)
    {
        parent :: __construct($browser, $condition);
    }

    function get_objects($offset, $count, $order_property = null)
    {
        $order_property = $this->get_order_property($order_property);
        return SurveyContextDataManager :: get_instance()->retrieve_survey_context_registrations($this->get_condition(), $offset, $count, $order_property);
    }

    function get_object_count()
    {
        return SurveyContextDataManager :: get_instance()->count_survey_context_registrations($this->get_condition());
    }
}
?>