<?php

require_once dirname(__FILE__) . '/rel_group_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/../../../tables/period_rel_group_table/default_period_rel_group_table_cell_renderer.class.php';

class InternshipOrganizerPeriodRelGroupBrowserTableCellRenderer extends DefaultInternshipOrganizerPeriodRelGroupTableCellRenderer
{
    
    private $browser;

    function InternshipOrganizerPeriodRelGroupBrowserTableCellRenderer($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $rel_group)
    {
        if ($column === InternshipOrganizerPeriodRelGroupBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($rel_group);
        }
        
        return parent :: render_cell($column, $rel_group);
    }

    private function get_modification_links($rel_group)
    {
        $toolbar = new Toolbar();
        
        if (InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: UNSUBSCRIBE_USER_RIGHT, $rel_group->get_period_id(), InternshipOrganizerRights :: TYPE_PERIOD))
        {
            $toolbar->add_item(new ToolbarItem(Translation :: get('Unsubscribe'), Theme :: get_common_image_path() . 'action_delete.png', $this->browser->get_period_unsubscribe_group_url($rel_group), ToolbarItem :: DISPLAY_ICON, true));
        }
        return $toolbar->as_html();
    }
}
?>