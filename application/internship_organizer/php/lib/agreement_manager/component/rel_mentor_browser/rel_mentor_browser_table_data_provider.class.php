<?php

class InternshipOrganizerAgreementRelMentorBrowserTableDataProvider extends ObjectTableDataProvider
{
    
    private $browser;

    function InternshipOrganizerAgreementRelMentorBrowserTableDataProvider($browser, $condition)
    {
        parent :: __construct($browser, $condition);
    
    }

    /**
     * Gets the Mentor objects
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
        
        return InternshipOrganizerDataManager :: get_instance()->retrieve_agreement_rel_mentors($this->get_condition(), $offset, $count, $order_property, $order_direction);
    }

    /**
     * Gets the number of rel_mentors in the table
     * @return int
     */
    function get_object_count()
    {
        return InternshipOrganizerDataManager :: get_instance()->count_agreement_rel_mentors($this->get_condition());
    }
}
?>