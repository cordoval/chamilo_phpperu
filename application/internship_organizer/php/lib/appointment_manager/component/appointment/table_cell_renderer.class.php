<?php

require_once WebApplication :: get_application_class_lib_path('internship_organizer') . 'appointment_manager/component/appointment/table_column_model.class.php';
require_once WebApplication :: get_application_class_lib_path('internship_organizer') . 'tables/appointment_table/default_appointment_table_cell_renderer.class.php';

class InternshipOrganizerAppointmentBrowserTableCellRenderer extends DefaultInternshipOrganizerAppointmentTableCellRenderer
{
    
    private $browser;

    function InternshipOrganizerAppointmentBrowserTableCellRenderer($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $appointment)
    {
        if ($column === InternshipOrganizerAppointmentBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($appointment);
        }
        
        return parent :: render_cell($column, $appointment);
    }

    function render_id_cell($appointment)
    {
        
        return $appointment->get_id();
    }

    /**
     * Gets the action links to display
     * @param Location $user The user for which the
     * action links should be returned
     * @return string A HTML representation of the action links
     */
    private function get_modification_links($appointment)
    {
       
        $toolbar = new Toolbar();
        
//        if (InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: RIGHT_ADD, InternshipOrganizerRights :: LOCATION_APPOINTMENT, InternshipOrganizerRights :: TYPE_COMPONENT))
//        {
//          $toolbar->add_item(new ToolbarItem(Translation :: get('MakeAppointment'), Theme :: get_common_image_path() . 'action_subscribe.png', $this->browser->get_create_appointment_url($appointment), ToolbarItem :: DISPLAY_ICON));
//        }
        return $toolbar->as_html();
    }
}
?>