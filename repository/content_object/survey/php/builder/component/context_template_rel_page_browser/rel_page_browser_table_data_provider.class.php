<?php

class SurveyContextTemplateRelPageBrowserTableDataProvider extends ObjectTableDataProvider
{

    /**
     * Constructor
     * @param RepositoryManagerComponent $browser
     * @param Condition $condition
     */
    function SurveyContextTemplateRelPageBrowserTableDataProvider($browser, $condition)
    {
        parent :: __construct($browser, $condition);
//   dump($this->get_condition());
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
    	return SurveyContextDataManager::get_instance()->retrieve_template_rel_pages($this->get_condition(), $offset, $count, $order_property);
    }

    /**
     * Gets the number of learning objects in the table
     * @return int
     */
    function get_object_count()
    {
    	return SurveyContextDataManager::get_instance()->count_template_rel_pages($this->get_condition());
    }
}
?>