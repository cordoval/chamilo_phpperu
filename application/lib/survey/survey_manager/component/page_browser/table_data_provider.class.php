<?php

class SurveyPageBrowserTableDataProvider extends ObjectTableDataProvider
{
	private $survey_ids;
    /**
     * Constructor
     * @param ApplicationComponent $browser
     * @param Condition $condition
     */
    function SurveyPageBrowserTableDataProvider($browser, $condition)
    {
        parent :: __construct($browser, $condition);
        $this->survey_ids = $browser->get_survey_ids();
    }

    /**
     * Retrieves the objects
     * @param int $offset
     * @param int $count
     * @param string $order_property
     * @return ResultSet A set of objects
     */
    function get_objects($offset, $count, $order_property = null)
    {
        $order_property = $this->get_order_property($order_property);
        return $this->get_browser()->retrieve_survey_pages($this->survey_ids, $this->get_condition(), $offset, $count, $order_property);
    }

    /**
     * Gets the number of objects in the table
     * @return int
     */
    function get_object_count()
    {
        return $this->get_browser()->count_survey_pages($this->survey_ids,$this->get_condition());
    }
}
?>