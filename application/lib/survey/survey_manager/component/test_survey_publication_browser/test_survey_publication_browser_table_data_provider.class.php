<?php
/**
 * $Id: survey_publication_browser_table_data_provider.class.php 193 2009-11-13 11:53:37Z chellee $
 * @package application.lib.survey.survey_manager.component.survey_publication_browser
 */
/**
 * Data provider for a survey_publication table
 *
 * @author Sven Vanpoucke
 * @author
 */
class TestSurveyPublicationBrowserTableDataProvider extends ObjectTableDataProvider
{

    /**
     * Constructor
     * @param ApplicationComponent $browser
     * @param Condition $condition
     */
    function TestSurveyPublicationBrowserTableDataProvider($browser, $condition)
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
        
        return $this->get_browser()->retrieve_survey_publications($this->get_condition(), $offset, $count, $order_property);
    }

    /**
     * Gets the number of objects in the table
     * @return int
     */
    function get_object_count()
    {
        return $this->get_browser()->count_survey_publications($this->get_condition());
    }
}
?>