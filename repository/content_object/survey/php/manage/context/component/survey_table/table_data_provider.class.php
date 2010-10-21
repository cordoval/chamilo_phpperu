<?php

class SurveyTableDataProvider extends ObjectTableDataProvider
{
	
	private $context_type;
    
	function SurveyTableDataProvider($component, $condition)
    {
       parent :: __construct($component, $condition);
    }

    function get_objects($offset, $count, $order_property = null)
    {
        $order_property = $this->get_order_property($order_property);
        return RepositoryDataManager :: get_instance()->retrieve_type_content_objects(Survey :: get_type_name(), $this->get_condition(), $order_property, $offset, $count);
    }

    function get_object_count()
    {
       return RepositoryDataManager :: get_instance()->count_type_content_objects(Survey :: get_type_name(), $this->get_condition());
    }
}
?>