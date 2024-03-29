<?php
namespace application\internship_organizer;

use common\libraries\ObjectTableDataProvider;

class InternshipOrganizerAppointmentBrowserTableDataProvider extends ObjectTableDataProvider
{

    function __construct($browser, $condition)
    {
        parent :: __construct($browser, $condition);
    }

    function get_objects($offset, $count, $order_property = null)
    {
        return InternshipOrganizerDataManager :: get_instance()->retrieve_appointments($this->get_condition(), $offset, $count, $order_property);
    }

    function get_object_count()
    {
        return InternshipOrganizerDataManager :: get_instance()->count_appointments($this->get_condition());
    }
}
?>