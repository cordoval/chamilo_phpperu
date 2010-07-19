<?php

class SurveyContextTableDataProvider extends ObjectTableDataProvider
{

    function SurveyContextTableDataProvider($component, $condition)
    {
        parent :: __construct($component, $condition);
    }

    function get_objects($offset, $count, $order_property = null)
    {
        $order_property = $this->get_order_property($order_property);
        return SurveyContextDataManager :: get_instance()->retrieve_contexts($this->get_condition(), $offset, $count, $order_property);
    }

    function get_object_count()
    {
        return SurveyContextDataManager :: get_instance()->count_contexts($this->get_condition());
    }
}
?>