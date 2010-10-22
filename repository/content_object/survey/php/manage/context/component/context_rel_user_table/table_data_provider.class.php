<?php namespace repository\content_object\survey;

class SurveyContextRelUserBrowserTableDataProvider extends ObjectTableDataProvider
{
    
    private $browser;

    function SurveyContextRelUserBrowserTableDataProvider($browser, $condition)
    {
        parent :: __construct($browser, $condition);
    
    }
   
    function get_objects($offset, $count, $order_property = null, $order_direction = null)
    {
        $order_property = $this->get_order_property($order_property);
        $order_direction = $this->get_order_property($order_direction);
        
        return SurveyContextDataManager :: get_instance()->retrieve_survey_context_rel_users($this->get_condition(), $offset, $count, $order_property, $order_direction);
    }

    function get_object_count()
    {
        return SurveyContextDataManager :: get_instance()->count_survey_context_rel_users($this->get_condition());
    }
}
?>