<?php

class SurveyContextTableDataProvider extends ObjectTableDataProvider
{
	
	private $context_type;
    
	function SurveyContextTableDataProvider($component, $condition, $context_type)
    {
        $this->context_type = $context_type;
    	parent :: __construct($component, $condition);
    }

    function get_objects($offset, $count, $order_property = null)
    {
        $order_property = $this->get_order_property($order_property);
        return SurveyContextDataManager :: get_instance()->retrieve_survey_contexts($this->context_type, $this->get_condition(), $offset, $count, $order_property);
    }

    function get_object_count()
    {
        return SurveyContextDataManager :: get_instance()->count_survey_contexts($this->context_type, $this->get_condition());
    }
}
?>