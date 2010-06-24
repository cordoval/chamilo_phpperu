<?php
/**
 * $Id: publication_browser_table_data_provider.class.php 193 2009-11-13 11:53:37Z chellee $
 * @package application.lib.survey.survey_manager.component.publication_browser
 */
/**
 * Data provider for a publication table
 *
 * @author Sven Vanpoucke
 * @author
 */
class InternshipOrganizerPublicationTableDataProvider extends ObjectTableDataProvider
{

    /**
     * Constructor
     * @param ApplicationComponent $browser
     * @param Condition $condition
     */
    function InternshipOrganizerPublicationTableDataProvider($browser, $condition)
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
        
        return InternshipOrganizerDataManager::get_instance()->retrieve_publications($this->get_condition(), $offset, $count, $order_property);
    }

    /**
     * Gets the number of objects in the table
     * @return int
     */
    function get_object_count()
    {
        return InternshipOrganizerDataManager::get_instance()->count_publications($this->get_condition());
    }
}
?>