<?php

class SurveyContextTemplateBrowserTableDataProvider extends ObjectTableDataProvider
{

    /**
     * Constructor
     * @param RepositoryManagerComponent $browser
     * @param Condition $condition
     */
    function SurveyContextTemplateBrowserTableDataProvider($browser, $condition)
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
        return SurveyContextDataManager::get_instance()->retrieve_survey_context_templates($this->get_condition(), $offset, $count, $order_property);
    }

    /**
     * Gets the number of learning objects in the table
     * @return int
     */
    function get_object_count()
    {
        return SurveyContextDataManager::get_instance()->count_survey_context_templates($this->get_condition());
    }
}
?>