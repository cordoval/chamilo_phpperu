<?php namespace repository\content_object\survey;

class SurveyPageTableDataProvider extends ObjectTableDataProvider
{

    /**
     * Constructor
     * @param ApplicationComponent $browser
     * @param Condition $condition
     */
    function SurveyPageTableDataProvider($browser, $condition)
    {
        parent :: __construct($browser, $condition);
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
        return RepositoryDataManager :: get_instance()->retrieve_type_content_objects(SurveyPage :: get_type_name(), $this->get_condition(), $order_property, $offset, $count);
    }

    /**
     * Gets the number of objects in the table
     * @return int
     */
    function get_object_count()
    {
        return RepositoryDataManager :: get_instance()->count_type_content_objects(SurveyPage :: get_type_name(), $this->get_condition());
    }
}
?>