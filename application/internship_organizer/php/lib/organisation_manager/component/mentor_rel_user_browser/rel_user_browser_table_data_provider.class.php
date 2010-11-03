<?php
namespace application\internship_organizer;


class InternshipOrganizerMentorRelUserBrowserTableDataProvider extends ObjectTableDataProvider
{
    
    private $browser;

    function InternshipOrganizerMentorRelUserBrowserTableDataProvider($browser, $condition)
    {
        parent :: __construct($browser, $condition);
    
    }

    /**
     * Gets the User objects
     * @param int $offset
     * @param int $count
     * @param string $order_property
     * @param int $order_direction (SORT_ASC or SORT_DESC)
     * @return ResultSet A set of matching student objects.
     */
    function get_objects($offset, $count, $order_property = null, $order_direction = null)
    {
        $order_property = $this->get_order_property($order_property);
        $order_direction = $this->get_order_property($order_direction);
        
        return InternshipOrganizerDataManager :: get_instance()->retrieve_mentor_rel_users($this->get_condition(), $offset, $count, $order_property, $order_direction);
    }

    /**
     * Gets the number of rel_users in the table
     * @return int
     */
    function get_object_count()
    {
        return InternshipOrganizerDataManager :: get_instance()->count_mentor_rel_users($this->get_condition());
    }
}
?>