<?php

require_once dirname(__FILE__) . '/browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/../../../tables/period_table/default_period_table_cell_renderer.class.php';
/**
 * Cell rendere for the learning object browser table
 */
class InternshipOrganizerPeriodBrowserTableCellRenderer extends DefaultInternshipOrganizerPeriodTableCellRenderer
{
    /**
     * The repository browser component
     */
    private $browser;

    /**
     * Constructor
     * @param RepositoryManagerBrowserComponent $browser
     */
    function InternshipOrganizerPeriodBrowserTableCellRenderer($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $period)
    {
        if ($column === InternshipOrganizerPeriodBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($period);
        }
        
        // Add special features here
        switch ($column->get_name())
        {
            // Exceptions that need post-processing go here ...
            case InternshipOrganizerPeriod :: PROPERTY_NAME :
                $title = parent :: render_cell($column, $period);
                $title_short = $title;
                if (strlen($title_short) > 53)
                {
                    $title_short = mb_substr($title_short, 0, 50) . '&hellip;';
                }
                return '<a href="' . htmlentities($this->browser->get_period_viewing_url($period)) . '" title="' . $title . '">' . $title_short . '</a>';
            case InternshipOrganizerPeriod :: PROPERTY_DESCRIPTION :
                $description = strip_tags(parent :: render_cell($column, $period));
                return Utilities :: truncate_string($description);
            case Translation :: get('InternshipOrganizerSubperiods') :
                return $period->count_children(true);
        }
        
        return parent :: render_cell($column, $period);
    }

    /**
     * Gets the action links to display
     * @param ContentObject $content_object The learning object for which the
     * action links should be returned
     * @return string A HTML representation of the action links
     */
    private function get_modification_links($period)
    {
        $toolbar = new Toolbar();
        
        if (InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: RIGHT_EDIT, InternshipOrganizerRights :: LOCATION_PERIOD, InternshipOrganizerRights :: TYPE_INTERNSHIP_ORGANIZER_COMPONENT))
        {
            $toolbar->add_item(new ToolbarItem(Translation :: get('Edit'), Theme :: get_common_image_path() . 'action_edit.png', $this->browser->get_period_editing_url($period), ToolbarItem :: DISPLAY_ICON));
        }
        if (InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: RIGHT_DELETE, InternshipOrganizerRights :: LOCATION_PERIOD, InternshipOrganizerRights :: TYPE_INTERNSHIP_ORGANIZER_COMPONENT))
        {
            $toolbar->add_item(new ToolbarItem(Translation :: get('Delete'), Theme :: get_common_image_path() . 'action_delete.png', $this->browser->get_period_delete_url($period), ToolbarItem :: DISPLAY_ICON, true));
        }
        if (InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: RIGHT_VIEW, InternshipOrganizerRights :: LOCATION_REPORTING, InternshipOrganizerRights :: TYPE_INTERNSHIP_ORGANIZER_COMPONENT))
        {
            $toolbar->add_item(new ToolbarItem(Translation :: get('Reporting'), Theme :: get_common_image_path() . 'action_view_results.png', $this->browser->get_period_reporting_url($period), ToolbarItem :: DISPLAY_ICON));
        }
        if ($this->browser->get_user()->is_platform_admin() || $period->get_owner() == $this->browser->get_user_id())
        {
            $toolbar->add_item(new ToolbarItem(Translation :: get('ManageRights'), Theme :: get_common_image_path() . 'action_rights.png', $this->browser->get_rights_editor_url($period), ToolbarItem :: DISPLAY_ICON));
        }
        return $toolbar->as_html();
    }
}
?>