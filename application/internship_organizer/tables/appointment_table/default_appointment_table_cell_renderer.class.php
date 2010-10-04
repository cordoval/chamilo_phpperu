<?php

require_once dirname(__FILE__) . '/../../appointment.class.php';

class DefaultInternshipOrganizerAppointmentTableCellRenderer extends ObjectTableCellRenderer
{

    /**
     * Constructor
     */
    function DefaultInternshipOrganizerAppointmentTableCellRenderer()
    {
    }

    function render_cell($column, $appointment)
    {
        
        switch ($column->get_name())
        {
            case InternshipOrganizerAppointment :: PROPERTY_TITLE :
                return $appointment->get_title();
            case InternshipOrganizerAppointment :: PROPERTY_DESCRIPTION :
                return $appointment->get_description();
            case InternshipOrganizerAppointment :: PROPERTY_STATUS :
                return InternshipOrganizerAppointment :: get_status_name($appointment->get_status());
             case InternshipOrganizerAppointment :: PROPERTY_TYPE :
                return InternshipOrganizerAppointment :: get_type_name($appointment->get_type());   
             case InternshipOrganizerAppointment :: PROPERTY_CREATED :
                return $this->get_date($appointment->get_created());
            default :
                return '&nbsp;';
        }
    }
	
	private function get_date($date)
    {
        if ($date == 0)
        {
            return Translation :: get('NoDate');
        }
        else
        {
            return date("Y-m-d H:i", $date);
        
        }
    }
    
    function render_id_cell($object)
    {
        return $object->get_id();
    }
}
?>