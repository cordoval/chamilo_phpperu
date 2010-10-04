<?php

require_once dirname(__FILE__) . '/table_column_model.class.php';
require_once dirname(__FILE__) . '/../../../tables/moment_rel_location_table/default_moment_rel_location_table_cell_renderer.class.php';

class InternshipOrganizerMomentRelLocationBrowserTableCellRenderer extends DefaultInternshipOrganizerMomentRelLocationTableCellRenderer
{
    
    private $browser;

    function InternshipOrganizerMomentRelLocationBrowserTableCellRenderer($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $moment)
    {
        if ($column === InternshipOrganizerMomentRelLocationBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($moment);
        }
        
        switch ($column->get_name())
        {
            case Translation :: get('Appointments') :
                $condition = new EqualityCondition(InternshipOrganizerAppointment::PROPERTY_MOMENT_ID, $moment->get_optional_property('moment_id'));
            	$appointment_count = InternshipOrganizerDataManager::get_instance()->count_appointments($condition);
            	return $appointment_count;
        }              
        
        
        return parent :: render_cell($column, $moment);
    }
  

    /**
     * Gets the action links to display
     * @param Location $user The user for which the
     * action links should be returned
     * @return string A HTML representation of the action links
     */
    private function get_modification_links($moment)
    {
       
        $toolbar = new Toolbar();
        
        if (InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: RIGHT_ADD, InternshipOrganizerRights :: LOCATION_APPOINTMENT, InternshipOrganizerRights :: TYPE_COMPONENT))
        {
          $toolbar->add_item(new ToolbarItem(Translation :: get('MakeAppointment'), Theme :: get_common_image_path() . 'action_subscribe.png', $this->browser->get_create_appointment_url($moment), ToolbarItem :: DISPLAY_ICON));
        }
        return $toolbar->as_html();
    }
}
?>