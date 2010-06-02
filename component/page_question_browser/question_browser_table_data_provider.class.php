<?php

class SurveyPageQuestionBrowserTableDataProvider extends ObjectTableDataProvider
{

    /**
     * Constructor
     * @param RepositoryManagerComponent $browser
     * @param Condition $condition
     */
    function SurveyPageQuestionBrowserTableDataProvider($browser, $condition)
    {
        parent :: __construct($browser, $condition);
    }

    /**
     * Gets the learning objects
     * @param int $offset
     * @param int $count
     * @param string $order_property
     * @return ResultSet A set of matching learning objects.
     */
    function get_objects($offset, $count, $order_property = null)
    {
    	$order_property = $this->get_order_property($order_property);
    	return RepositoryDataManager::get_instance()->retrieve_complex_content_object_items($this->get_condition(), $order_property, $offset, $count );
    }

    /**
     * Gets the number of learning objects in the table
     * @return int
     */
    function get_object_count()
    {
      	return RepositoryDataManager::get_instance()->count_complex_content_object_items($this->get_condition());
    }
}
?>