<?php
namespace application\internship_organizer;


class InternshipOrganizerMentorBrowserTableDataProvider extends ObjectTableDataProvider
{

    /**
     * Constructor
     * @param ApplicationComponent $browser
     * @param Condition $condition
     */
    function InternshipOrganizerMentorBrowserTableDataProvider($browser, $condition)
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
        
        return InternshipOrganizerDataManager::get_instance()->retrieve_mentor_rel_locations($this->get_condition(), $offset, $count, $order_property);
    }

    /**
     * Gets the number of objects in the table
     * @return int
     */
    function get_object_count()
    {
        return InternshipOrganizerDataManager::get_instance()->count_mentor_rel_locations($this->get_condition());
    }
}
?>