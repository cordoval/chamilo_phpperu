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
            return $this->get_modification_links( $user );
        }
        return parent :: render_cell($column, $user);
    }

    private function get_modification_links($user)
    {
    	$toolbar = new Toolbar();
    	$toolbar->add_item(new ToolbarItem(Translation :: get('Unsubscribe'), Theme :: get_common_image_path() . 'action_delete.png', $this->browser->get_period_unsubscribe_user_url($user), ToolbarItem :: DISPLAY_ICON, true));
//    	$toolbar_data = array();
        
    	return $toolbar->as_html();
    	
//        return Utilities :: build_toolbar($toolbar_data);
    }
    
}
?>