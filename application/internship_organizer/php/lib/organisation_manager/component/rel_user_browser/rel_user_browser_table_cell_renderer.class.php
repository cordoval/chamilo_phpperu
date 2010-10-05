<?php

require_once dirname(__FILE__) . '/rel_user_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/../../../tables/organisation_rel_user_table/default_organisation_rel_user_table_cell_renderer.class.php';

class InternshipOrganizerOrganisationRelUserBrowserTableCellRenderer extends DefaultInternshipOrganizerOrganisationRelUserTableCellRenderer
{
    
    private $browser;

    function InternshipOrganizerOrganisationRelUserBrowserTableCellRenderer($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $rel_user)
    {
        if ($column === InternshipOrganizerOrganisationRelUserBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links( $rel_user);
        }
        
        return parent :: render_cell($column, $rel_user);
    }

    private function get_modification_links($rel_user)
    {
        $toolbar = new Toolbar();
        
        if (InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: RIGHT_EDIT, InternshipOrganizerRights :: LOCATION_ORGANISATION, InternshipOrganizerRights :: TYPE_COMPONENT))
        {
            $toolbar->add_item(new ToolbarItem(Translation :: get('Delete'), Theme :: get_common_image_path() . 'action_delete.png', $this->browser->get_unsubscribe_user_url($rel_user), ToolbarItem :: DISPLAY_ICON, true));
        }
        
        return $toolbar->as_html();
    }

}
?>