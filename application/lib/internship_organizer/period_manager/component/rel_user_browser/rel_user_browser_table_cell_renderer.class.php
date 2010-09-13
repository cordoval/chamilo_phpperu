<?php

require_once dirname(__FILE__) . '/rel_user_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/../../../tables/period_rel_user_table/default_period_rel_user_table_cell_renderer.class.php';

class InternshipOrganizerPeriodRelUserBrowserTableCellRenderer extends DefaultInternshipOrganizerPeriodRelUserTableCellRenderer
{
    
    private $browser;

    function InternshipOrganizerPeriodRelUserBrowserTableCellRenderer($browser)
    {
        parent :: __construct($browser->get_period());
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $rel_user)
    {
        if ($column === InternshipOrganizerPeriodRelUserBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($rel_user);
        }
        
        return parent :: render_cell($column, $rel_user);
    }

    private function get_modification_links($rel_user)
    {
        $toolbar = new Toolbar();
        
        if (InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: UNSUBSCRIBE_USER_RIGHT, $rel_user->get_period_id(), InternshipOrganizerRights :: TYPE_PERIOD))
        {
            $toolbar->add_item(new ToolbarItem(Translation :: get('Unsubscribe'), Theme :: get_common_image_path() . 'action_delete.png', $this->browser->get_period_unsubscribe_user_url($rel_user), ToolbarItem :: DISPLAY_ICON, true));
        }
        return $toolbar->as_html();
    }
}
?>