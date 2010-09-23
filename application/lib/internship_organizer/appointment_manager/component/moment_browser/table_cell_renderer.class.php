<?php

require_once dirname(__FILE__) . '/table_column_model.class.php';
require_once dirname(__FILE__) . '/../../../tables/moment_table/default_moment_table_cell_renderer.class.php';

class InternshipOrganizerMomentRelUserBrowserTableCellRenderer extends DefaultInternshipOrganizerMomentTableCellRenderer
{
    
    private $browser;

    function InternshipOrganizerMomentRelUserBrowserTableCellRenderer($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $moment)
    {
        if ($column === InternshipOrganizerMomentRelUserBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($moment);
        }
        
        return parent :: render_cell($column, $moment);
    }

    function render_id_cell($moment)
    {
        
        return $moment->get_id();
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