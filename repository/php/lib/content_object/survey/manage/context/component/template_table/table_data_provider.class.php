<?php

class SurveyTemplateTableDataProvider extends ObjectTableDataProvider
{
	
	private $template_type;
    
	function SurveyTemplateTableDataProvider($component, $condition, $template_type)
    {
        $this->template_type = $template_type;
    	parent :: __construct($component, $condition);
    }

    function get_objects($offset, $count, $order_property = null)
    {
        $order_property = $this->get_order_property($order_property);
        return SurveyContextDataManager :: get_instance()->retrieve_survey_templates($this->template_type, $this->get_condition(), $offset, $count, $order_property);
    }

    function get_object_count()
    {
        return SurveyContextDataManager :: get_instance()->count_survey_templates($this->template_type, $this->get_condition());
    }
}
?>