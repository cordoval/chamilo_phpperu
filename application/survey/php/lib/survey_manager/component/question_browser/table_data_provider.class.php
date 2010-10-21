<?php namespace survey;

class SurveyQuestionBrowserTableDataProvider extends ObjectTableDataProvider
{

    /**
     * Constructor
     * @param ApplicationComponent $browser
     * @param Condition $condition
     */
    function SurveyQuestionBrowserTableDataProvider($browser, $condition)
    {
        parent :: __construct($browser, $condition);
        $this->page_ids = $browser->get_page_ids();
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
        return SurveyDataManager :: get_instance()->retrieve_survey_questions($this->page_ids, $this->get_condition(), $offset, $count, $order_property);
    }

    /**
     * Gets the number of objects in the table
     * @return int
     */
    function get_object_count()
    {
        return SurveyDataManager :: get_instance()->count_survey_questions($this->page_ids, $this->get_condition());
    }
}
?>