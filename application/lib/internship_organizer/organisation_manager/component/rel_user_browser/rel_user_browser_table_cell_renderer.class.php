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
            //return $this->get_modification_links( $rel_user);
        }
        
        return parent :: render_cell($column, $rel_user);
    }

    private function get_modification_links($rel_user)
    {
        $toolbar_data = array();
        
        return Utilities :: build_toolbar($toolbar_data);
    }
}
?>