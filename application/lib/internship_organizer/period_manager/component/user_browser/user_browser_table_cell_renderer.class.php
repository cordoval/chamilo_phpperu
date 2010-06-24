<?php

require_once dirname(__FILE__).'/../../../tables/user_table/default_user_table_cell_renderer.class.php';
require_once Path :: get_user_path() . '/lib/user_table/default_user_table_cell_renderer.class.php';

class InternshipOrganizerPeriodUserBrowserTableCellRenderer extends DefaultInternshipOrganizerUserTableCellRenderer
{
    
    private $browser;

    function InternshipOrganizerPeriodUserBrowserTableCellRenderer($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $user)
    {
        if ($column === InternshipOrganizerPeriodUserBrowserTableColumnModel :: get_modification_column())
        {
            //return $this->get_modification_links( $user);
        }
        return parent :: render_cell($column, $user);
    }

    private function get_modification_links($user)
    {
        $toolbar_data = array();
        
        return Utilities :: build_toolbar($toolbar_data);
    }
}
?>